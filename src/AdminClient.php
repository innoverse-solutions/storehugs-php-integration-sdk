<?php

declare(strict_types=1);

namespace StoreHugs\Integration;

use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\Admin\Coupons;
use StoreHugs\Integration\Admin\Customers;
use StoreHugs\Integration\Admin\Keys;
use StoreHugs\Integration\Admin\Offers;
use StoreHugs\Integration\Admin\Orders;
use StoreHugs\Integration\Admin\Products;
use StoreHugs\Integration\Admin\Quotations;
use StoreHugs\Integration\Admin\Reviews;
use StoreHugs\Integration\Admin\WebhookEndpoints;
use StoreHugs\Integration\Http\HttpOptions;
use StoreHugs\Integration\Http\Transport;

/**
 * The Admin API (`/api/admin/v1` on the panel host), for the merchant's own
 * systems and apps they connect. The key's scopes decide what each call may
 * do; a missing scope is 403 `insufficient_scope`.
 *
 * @phpstan-import-type AdminShop from Schemas
 * @phpstan-import-type InventoryItemInput from Schemas
 * @phpstan-import-type InventoryResult from Schemas
 * @phpstan-import-type AppTokenResponse from Schemas
 */
final class AdminClient
{
    public const DEFAULT_BASE_URL = 'https://my.storehugs.com';

    public readonly Keys $keys;

    public readonly Products $products;

    public readonly Orders $orders;

    public readonly Customers $customers;

    public readonly Coupons $coupons;

    public readonly Offers $offers;

    public readonly Reviews $reviews;

    public readonly Quotations $quotations;

    public readonly WebhookEndpoints $webhooks;

    private readonly Transport $transport;

    /**
     * @param  string  $apiKey  An Admin key, `sa_live_…`: made in the panel, or issued to your app. Keep it on your server.
     * @param  string  $baseUrl  The panel's origin.
     */
    public function __construct(
        string $apiKey,
        string $baseUrl = self::DEFAULT_BASE_URL,
        HttpOptions $http = new HttpOptions,
    ) {
        $this->transport = new Transport(self::apiUrl($baseUrl), ['Authorization' => "Bearer {$apiKey}"], $http);
        $this->keys = new Keys($this->transport, '/keys');
        $this->products = new Products($this->transport, '/products');
        $this->orders = new Orders($this->transport, '/orders');
        $this->customers = new Customers($this->transport, '/customers');
        $this->coupons = new Coupons($this->transport, '/coupons');
        $this->offers = new Offers($this->transport, '/offers');
        $this->reviews = new Reviews($this->transport, '/reviews');
        $this->quotations = new Quotations($this->transport, '/quotations');
        $this->webhooks = new WebhookEndpoints($this->transport, '/webhooks');
    }

    /**
     * @return AdminShop
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function shop(): array
    {
        return $this->transport->data('GET', '/shop');
    }

    /**
     * Sets stock for up to 100 products, by id or SKU; all or nothing.
     *
     * @param  list<InventoryItemInput>  $items
     * @return list<InventoryResult>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function setInventory(array $items, string|false|null $idempotencyKey = null): array
    {
        return $this->transport->data('POST', '/inventory', body: ['items' => $items], idempotencyKey: $idempotencyKey);
    }

    /**
     * The page to send a merchant to, to approve your app for an Admin key
     * (OAuth authorization code with S256 PKCE).
     *
     * @param  list<string>  $scopes
     */
    public static function authorizeAppUrl(
        string $clientId,
        string $redirectUri,
        array $scopes,
        string $state,
        string $codeChallenge,
        string $baseUrl = self::DEFAULT_BASE_URL,
    ): string {
        return rtrim($baseUrl, '/').'/integrations/authorize?'.http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => implode(' ', $scopes),
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ], '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Your server trades the one-time code (10 minutes, one try) and your
     * PKCE verifier for the app's Admin key, shown this once.
     *
     * @return AppTokenResponse
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public static function exchangeAppCode(
        string $code,
        string $clientId,
        string $redirectUri,
        string $codeVerifier,
        string $baseUrl = self::DEFAULT_BASE_URL,
        HttpOptions $http = new HttpOptions,
    ): array {
        $transport = new Transport(self::apiUrl($baseUrl), [], new HttpOptions(
            $http->client,
            $http->requestFactory,
            $http->streamFactory,
            maxRetries: 0,
            sleep: $http->sleep,
            timeout: $http->timeout,
            connectTimeout: $http->connectTimeout,
        ));

        return $transport->request('POST', '/oauth/token', body: [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'code_verifier' => $codeVerifier,
        ], idempotencyKey: false);
    }

    private static function apiUrl(string $baseUrl): string
    {
        return rtrim($baseUrl, '/').'/api/admin/v1';
    }
}
