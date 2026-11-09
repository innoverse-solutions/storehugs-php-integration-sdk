<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Tests;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use RuntimeException;
use StoreHugs\Integration\Admin\QuotationAction;
use StoreHugs\Integration\AdminClient;
use StoreHugs\Integration\Http\HttpOptions;
use StoreHugs\Integration\IntegrationApiException;

final class AdminClientTest extends TestCase
{
    public function test_it_calls_the_panel_host_with_the_admin_key_and_unwraps_data(): void
    {
        $client = new FakeClient(FakeClient::json(['data' => ['id' => 7, 'name' => 'Acme']]));
        $admin = new AdminClient('sa_live_x', http: $client->options());

        $shop = $admin->shop();

        $this->assertSame('Acme', $shop['name']);
        $this->assertSame('https://my.storehugs.com/api/admin/v1/shop', (string) $client->request(0)->getUri());
        $this->assertSame('Bearer sa_live_x', $client->request(0)->getHeaderLine('Authorization'));
        $this->assertFalse($client->request(0)->hasHeader('Idempotency-Key'));
    }

    public function test_every_write_carries_an_idempotency_key_kept_across_a_retry_after_429(): void
    {
        $client = new FakeClient(
            FakeClient::json(['message' => 'Too many'], 429, ['Retry-After' => '3']),
            FakeClient::json(['data' => ['id' => 1]], 201),
        );
        $admin = new AdminClient('sa_live_x', http: $client->options());

        $admin->coupons->create(['code' => 'SUMMER', 'type' => 'percentage', 'value' => 10]);

        $this->assertCount(2, $client->requests);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $client->request(0)->getHeaderLine('Idempotency-Key'));
        $this->assertSame($client->request(0)->getHeaderLine('Idempotency-Key'), $client->request(1)->getHeaderLine('Idempotency-Key'));
        $this->assertSame([3.0], $client->sleeps);
        $this->assertSame(['code' => 'SUMMER', 'type' => 'percentage', 'value' => 10], $client->body(0));
    }

    public function test_a_network_error_is_retried_with_the_same_key(): void
    {
        $client = new FakeClient(
            fn (RequestInterface $request) => throw new class('Connection reset', $request) extends RuntimeException implements NetworkExceptionInterface
            {
                public function __construct(string $message, private readonly RequestInterface $request)
                {
                    parent::__construct($message);
                }

                public function getRequest(): RequestInterface
                {
                    return $this->request;
                }
            },
            FakeClient::json(['data' => [['id' => 1, 'stockQuantity' => 40]]]),
        );
        $admin = new AdminClient('sa_live_x', http: $client->options());

        $admin->setInventory([['sku' => 'MUG-01', 'stockQuantity' => 40]]);

        $this->assertCount(2, $client->requests);
        $this->assertSame($client->request(0)->getHeaderLine('Idempotency-Key'), $client->request(1)->getHeaderLine('Idempotency-Key'));
        $this->assertSame([0.5], $client->sleeps);
    }

    public function test_the_default_guzzle_client_retries_a_refused_connection_then_throws_it(): void
    {
        $sleeps = [];
        $admin = new AdminClient('sa_live_x', 'http://127.0.0.1:1', new HttpOptions(
            sleep: function (float $seconds) use (&$sleeps): void {
                $sleeps[] = $seconds;
            },
            connectTimeout: 1,
        ));

        try {
            $admin->shop();
            $this->fail('No exception was thrown.');
        } catch (ConnectException $exception) {
            $this->assertInstanceOf(NetworkExceptionInterface::class, $exception);
        }

        $this->assertSame([0.5, 1.0], $sleeps);
    }

    public function test_an_update_names_the_version_it_changes(): void
    {
        $client = new FakeClient(FakeClient::json(['data' => ['id' => 3], 'warnings' => []]));
        $admin = new AdminClient('sa_live_x', http: $client->options());

        $admin->products->update(3, ['title' => 'Mug'], unmodifiedSince: '2026-11-07T13:00:00+03:00');

        $this->assertSame('PATCH', $client->request(0)->getMethod());
        $this->assertSame('Sat, 07 Nov 2026 10:00:00 GMT', $client->request(0)->getHeaderLine('If-Unmodified-Since'));
    }

    public function test_all_follows_cursors_until_the_last_page(): void
    {
        $page = fn (array $ids, ?string $next) => FakeClient::json([
            'data' => array_map(fn (int $id) => ['id' => $id], $ids),
            'links' => [],
            'meta' => ['nextCursor' => $next],
        ]);
        $client = new FakeClient($page([1, 2], 'abc'), $page([3], null));
        $admin = new AdminClient('sa_live_x', http: $client->options());

        $ids = [];

        foreach ($admin->orders->all(['status' => 'confirmed', 'limit' => 2]) as $order) {
            $ids[] = $order['id'];
        }

        $this->assertSame([1, 2, 3], $ids);
        $this->assertSame('https://my.storehugs.com/api/admin/v1/orders?status=confirmed&limit=2&cursor=abc', (string) $client->request(1)->getUri());
    }

    public function test_a_refusal_throws_with_its_code_and_field_errors(): void
    {
        $client = new FakeClient(FakeClient::json(['message' => 'The value is invalid.', 'errors' => ['value' => ['Too high.']]], 422));
        $admin = new AdminClient('sa_live_x', http: $client->options(maxRetries: 0));

        try {
            $admin->coupons->update(1, ['value' => 150]);
            $this->fail('No exception was thrown.');
        } catch (IntegrationApiException $exception) {
            $this->assertSame(422, $exception->status);
            $this->assertSame(422, $exception->getCode());
            $this->assertSame('The value is invalid.', $exception->getMessage());
            $this->assertTrue($exception->isValidationError());
            $this->assertSame(['Too high.'], $exception->errors['value']);
        }
    }

    public function test_a_quotation_action_is_posted_to_its_own_path_with_the_order_made(): void
    {
        $client = new FakeClient(FakeClient::json([
            'data' => ['id' => 5, 'status' => 'agreed'],
            'order' => ['id' => 9, 'orderNumber' => 'A-9'],
        ]));
        $admin = new AdminClient('sa_live_x', http: $client->options());

        $result = $admin->quotations->perform(5, QuotationAction::AgreeAndOrder, 'By phone');

        $this->assertSame('https://my.storehugs.com/api/admin/v1/quotations/5/agree_and_order', (string) $client->request(0)->getUri());
        $this->assertSame(['note' => 'By phone'], $client->body(0));
        $this->assertSame(9, $result['order']['id'] ?? null);
    }

    public function test_an_action_without_a_note_sends_an_empty_object(): void
    {
        $client = new FakeClient(FakeClient::json(['data' => ['id' => 5], 'order' => null]));
        $admin = new AdminClient('sa_live_x', http: $client->options());

        $admin->quotations->perform(5, QuotationAction::Publish);

        $this->assertSame('{}', (string) $client->request(0)->getBody());
    }

    public function test_an_app_is_sent_to_approve_then_trades_its_code_for_an_admin_key(): void
    {
        $url = AdminClient::authorizeAppUrl(
            clientId: 'app-1',
            redirectUri: 'https://app.test/cb',
            scopes: ['orders:read', 'products:write'],
            state: 's',
            codeChallenge: 'c',
        );
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        $this->assertStringStartsWith('https://my.storehugs.com/integrations/authorize?', $url);
        $this->assertSame('orders:read products:write', $query['scope']);
        $this->assertSame('S256', $query['code_challenge_method']);

        $client = new FakeClient(FakeClient::json(['api_key' => 'sa_live_app', 'scopes' => ['orders:read'], 'shop' => ['id' => 1, 'name' => 'Acme']]));
        $grant = AdminClient::exchangeAppCode('code', 'app-1', 'https://app.test/cb', 'v', http: $client->options());

        $this->assertSame('sa_live_app', $grant['api_key']);
        $this->assertFalse($client->request(0)->hasHeader('Idempotency-Key'));
        $this->assertFalse($client->request(0)->hasHeader('Authorization'));
        $this->assertSame('authorization_code', $client->body(0)['grant_type'] ?? null);
    }

    public function test_a_retry_that_finds_the_first_write_still_running_waits_for_it_under_the_same_key(): void
    {
        $client = new FakeClient(
            FakeClient::json(['message' => 'Unavailable'], 503, ['Retry-After' => '0']),
            FakeClient::json(['message' => 'Still running', 'code' => 'idempotency_in_progress'], 409, ['Retry-After' => '1']),
            FakeClient::json(['message' => 'Still running', 'code' => 'idempotency_in_progress'], 409, ['Retry-After' => '1']),
            FakeClient::json(['data' => ['id' => 8]], 201, ['Idempotent-Replayed' => 'true']),
        );
        $admin = new AdminClient('sa_live_x', http: $client->options(maxRetries: 1));

        $coupon = $admin->coupons->create(['code' => 'WAIT', 'type' => 'fixed', 'value' => 5]);

        $this->assertSame(8, $coupon['id']);
        $this->assertCount(4, $client->requests);
        $this->assertCount(1, array_unique(array_map(fn (RequestInterface $request) => $request->getHeaderLine('Idempotency-Key'), $client->requests)));
    }

    public function test_another_409_is_thrown_at_once(): void
    {
        $client = new FakeClient(FakeClient::json(['message' => 'Changed since read', 'code' => 'edit_conflict'], 409));
        $admin = new AdminClient('sa_live_x', http: $client->options());

        try {
            $admin->coupons->update(1, ['value' => 5]);
            $this->fail('No exception was thrown.');
        } catch (IntegrationApiException $exception) {
            $this->assertSame('edit_conflict', $exception->errorCode);
        }

        $this->assertCount(1, $client->requests);
    }

    public function test_a_write_sent_without_a_key_is_not_retried(): void
    {
        $client = new FakeClient(FakeClient::json(['message' => 'Unavailable'], 503));
        $admin = new AdminClient('sa_live_x', http: $client->options());

        $this->expectException(IntegrationApiException::class);

        try {
            $admin->coupons->create(['code' => 'X', 'type' => 'fixed', 'value' => 1], idempotencyKey: false);
        } finally {
            $this->assertCount(1, $client->requests);
            $this->assertFalse($client->request(0)->hasHeader('Idempotency-Key'));
        }
    }

    public function test_a_custom_panel_origin_is_used(): void
    {
        $client = new FakeClient(FakeClient::json(null, 204));
        $admin = new AdminClient('sa_live_x', 'https://panel.test/', $client->options());

        $admin->keys->revoke(4);

        $this->assertInstanceOf(Request::class, $client->request(0));
        $this->assertSame('DELETE', $client->request(0)->getMethod());
        $this->assertSame('https://panel.test/api/admin/v1/keys/4', (string) $client->request(0)->getUri());
    }

    public function test_units_are_listed_by_search_and_changed_by_id(): void
    {
        $client = new FakeClient(
            FakeClient::json(['data' => [['id' => 4]], 'links' => [], 'meta' => ['nextCursor' => null]]),
            FakeClient::json(['data' => ['id' => 4, 'name' => ['en' => 'Litre']]]),
        );
        $admin = new AdminClient('sa_live_x', http: $client->options());

        $admin->units->list(['search' => 'lit']);
        $unit = $admin->units->update(4, ['translations' => ['ar' => 'لتر']], unmodifiedSince: '2026-11-08T10:00:00Z');

        $this->assertSame('https://my.storehugs.com/api/admin/v1/units?search=lit', (string) $client->request(0)->getUri());
        $this->assertSame('PATCH', $client->request(1)->getMethod());
        $this->assertSame('https://my.storehugs.com/api/admin/v1/units/4', (string) $client->request(1)->getUri());
        $this->assertSame('Sun, 08 Nov 2026 10:00:00 GMT', $client->request(1)->getHeaderLine('If-Unmodified-Since'));
        $this->assertSame(['translations' => ['ar' => 'لتر']], $client->body(1));
        $this->assertSame(4, $unit['id']);
    }
}
