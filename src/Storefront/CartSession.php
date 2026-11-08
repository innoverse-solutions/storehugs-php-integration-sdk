<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Storefront;

use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\Http\Transport;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * One cart, by its token: every change answers with the whole cart.
 *
 * @phpstan-import-type Cart from Schemas
 * @phpstan-import-type ShippingDestination from Schemas
 * @phpstan-import-type ShippingEstimate from Schemas
 * @phpstan-import-type CheckoutOptions from Schemas
 * @phpstan-import-type CheckoutLink from Schemas
 */
final class CartSession
{
    /** @internal */
    public function __construct(
        private readonly Transport $transport,
        public readonly string $token,
    ) {}

    /**
     * @return Cart
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function get(): array
    {
        return $this->send('GET', '/cart');
    }

    /**
     * @return Cart
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function addItem(int $productId, int $quantity = 1, string|false|null $idempotencyKey = null): array
    {
        return $this->send('POST', '/cart/items', ['productId' => $productId, 'quantity' => $quantity], $idempotencyKey);
    }

    /**
     * Sets a line's quantity; 0 removes the line.
     *
     * @return Cart
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function updateItem(int $itemId, int $quantity, string|false|null $idempotencyKey = null): array
    {
        return $this->send('PATCH', "/cart/items/{$itemId}", ['quantity' => $quantity], $idempotencyKey);
    }

    /**
     * @return Cart
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function removeItem(int $itemId, string|false|null $idempotencyKey = null): array
    {
        return $this->send('DELETE', "/cart/items/{$itemId}", null, $idempotencyKey);
    }

    /**
     * @return Cart
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function applyCoupon(string $code, string|false|null $idempotencyKey = null): array
    {
        return $this->send('POST', '/cart/coupon', ['code' => $code], $idempotencyKey);
    }

    /**
     * @return Cart
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function removeCoupon(string|false|null $idempotencyKey = null): array
    {
        return $this->send('DELETE', '/cart/coupon', null, $idempotencyKey);
    }

    /**
     * Only when the shop's shipping calculator is on.
     *
     * @param  ShippingDestination  $destination
     * @return ShippingEstimate
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function estimateShipping(array $destination, string|false|null $idempotencyKey = null): array
    {
        return $this->send('POST', '/cart/shipping-estimate', $destination, $idempotencyKey);
    }

    /**
     * A single-use link to the shop's checkout (30 minutes), to open in the shopper's browser.
     *
     * @param  CheckoutOptions  $options
     * @return CheckoutLink
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function checkout(array $options = [], string|false|null $idempotencyKey = null): array
    {
        return $this->send('POST', '/cart/checkout', $options, $idempotencyKey);
    }

    /**
     * @param  array<array-key, mixed>|null  $body
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    private function send(string $method, string $path, ?array $body = null, string|false|null $idempotencyKey = null): mixed
    {
        return $this->transport->data($method, $path, body: $body, headers: ['X-Cart-Token' => $this->token], idempotencyKey: $idempotencyKey);
    }
}
