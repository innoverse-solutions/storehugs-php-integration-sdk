<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use StoreHugs\Integration\Pkce;
use StoreHugs\Integration\Webhooks;

final class WebhooksTest extends TestCase
{
    private const BODY = '{"id":"e1","type":"order.created","data":{}}';

    private const T = 1_800_000_000;

    public function test_a_delivery_signed_with_the_secret_recently_is_genuine(): void
    {
        $header = 't='.self::T.',v1='.self::sign('whsec_a');

        $this->assertTrue(Webhooks::verifySignature(self::BODY, $header, 'whsec_a', now: self::T + 10));
        $this->assertFalse(Webhooks::verifySignature(self::BODY, $header, 'whsec_b', now: self::T + 10));
        $this->assertFalse(Webhooks::verifySignature(self::BODY.' ', $header, 'whsec_a', now: self::T + 10));
        $this->assertFalse(Webhooks::verifySignature(self::BODY, null, 'whsec_a'));
        $this->assertFalse(Webhooks::verifySignature(self::BODY, 'v1='.self::sign('whsec_a'), 'whsec_a', now: self::T));
    }

    public function test_an_old_timestamp_is_refused_and_either_of_two_signatures_passes_while_a_secret_is_replaced(): void
    {
        $this->assertFalse(Webhooks::verifySignature(self::BODY, 't='.self::T.',v1='.self::sign('whsec_a'), 'whsec_a', now: self::T + 301));
        $this->assertTrue(Webhooks::verifySignature(
            self::BODY,
            't='.self::T.',v1='.self::sign('whsec_new').',v1='.self::sign('whsec_old'),
            'whsec_old',
            now: self::T,
        ));
    }

    public function test_an_empty_secret_is_refused_rather_than_accepting_a_delivery_signed_with_an_empty_key(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Webhooks::verifySignature(self::BODY, 't='.self::T.',v1='.self::sign(''), '', now: self::T);
    }

    public function test_a_pkce_pair_is_a_verifier_and_its_s256_challenge(): void
    {
        $pair = Pkce::create();

        $this->assertMatchesRegularExpression('/^[A-Za-z0-9_-]{43}$/', $pair->verifier);
        $this->assertSame(rtrim(strtr(base64_encode(hash('sha256', $pair->verifier, true)), '+/', '-_'), '='), $pair->challenge);
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9_-]{22}$/', Pkce::state());
    }

    private static function sign(string $secret): string
    {
        return hash_hmac('sha256', self::T.'.'.self::BODY, $secret);
    }
}
