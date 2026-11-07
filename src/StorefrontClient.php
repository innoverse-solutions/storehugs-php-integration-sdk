<?php

declare(strict_types=1);

namespace StoreHugs\Integration;

use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\Http\HttpOptions;
use StoreHugs\Integration\Http\Transport;
use StoreHugs\Integration\Storefront\CartSession;
use StoreHugs\Integration\Storefront\CustomerAccount;
use StoreHugs\Integration\Storefront\Products;
use StoreHugs\Integration\Storefront\SignIn;

/**
 * The Storefront API (`/api/v1` on the shop's host), for a merchant's own
 * storefront: the catalog, carts with checkout on the shop's checkout page,
 * and signed-in shoppers.
 *
 * @phpstan-import-type StorefrontShop from Schemas
 * @phpstan-import-type CategoryType from Schemas
 * @phpstan-import-type StorefrontPage from Schemas
 * @phpstan-import-type Cart from Schemas
 */
final class StorefrontClient
{
    public readonly Products $products;

    /** The signed-in shopper's own data; needs an access token. */
    public readonly CustomerAccount $customer;

    /** Shopper sign-in on the shop's own page. */
    public readonly SignIn $signIn;

    private readonly Transport $transport;

    private readonly string $origin;

    /**
     * @param  string  $host  The shop's storefront host or custom domain, e.g. `acme.storehugs.com`.
     * @param  string  $key  A publishable key (browsers on the sites it lists) or a secret key (servers only).
     * @param  string|null  $shopperIp  With a secret key: the shopper your server calls for, so limits count per shopper.
     * @param  string|null  $accessToken  A signed-in shopper's access token (see `$signIn`).
     * @param  string|null  $locale  The language of names and descriptions; the shop's default otherwise.
     */
    public function __construct(
        private readonly string $host,
        private readonly string $key,
        private readonly ?string $shopperIp = null,
        ?string $accessToken = null,
        private readonly ?string $locale = null,
        private readonly HttpOptions $http = new HttpOptions,
    ) {
        $this->origin = rtrim(preg_match('#^https?://#', $host) === 1 ? $host : "https://{$host}", '/');
        $this->transport = new Transport("{$this->origin}/api/v1", [
            'X-Storehugs-Key' => $key,
            'X-Shopper-Ip' => $shopperIp,
            'Authorization' => $accessToken === null ? null : "Bearer {$accessToken}",
            'Accept-Language' => $locale,
        ], $http);
        $this->products = new Products($this->transport);
        $this->customer = new CustomerAccount($this->transport);
        $this->signIn = new SignIn($this->transport, $this->origin);
    }

    /**
     * @return StorefrontShop
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function shop(): array
    {
        return $this->transport->data('GET', '/shop');
    }

    /**
     * @return list<CategoryType>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function categories(): array
    {
        return $this->transport->data('GET', '/categories');
    }

    /**
     * @param  'about'|'contact-us'|'terms-conditions'|'privacy-policy'  $page
     * @return StorefrontPage
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function page(string $page): array
    {
        return $this->transport->data('GET', "/pages/{$page}");
    }

    /**
     * A new cart; keep its `cart_token` (with a shopper's token, the cart is theirs).
     *
     * @return Cart
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function createCart(string|false|null $idempotencyKey = null): array
    {
        return $this->transport->data('POST', '/carts', idempotencyKey: $idempotencyKey);
    }

    /** The cart a token names. */
    public function cart(string $cartToken): CartSession
    {
        return new CartSession($this->transport, $cartToken);
    }

    /** The same client acting for a signed-in shopper. */
    public function withAccessToken(string $accessToken): self
    {
        return new self($this->host, $this->key, $this->shopperIp, $accessToken, $this->locale, $this->http);
    }
}
