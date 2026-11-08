<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Storefront;

use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\Http\Transport;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * The shopper's wishlist: every change answers with the whole list.
 *
 * @phpstan-import-type ProductSummary from Schemas
 */
final class Wishlist
{
    /** @internal */
    public function __construct(private readonly Transport $transport) {}

    /**
     * @return list<ProductSummary>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function list(): array
    {
        return $this->transport->data('GET', '/customer/wishlist');
    }

    /**
     * @return list<ProductSummary>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function add(int $productId): array
    {
        return $this->transport->data('POST', '/customer/wishlist', body: ['productId' => $productId]);
    }

    /**
     * @return list<ProductSummary>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function remove(int $productId): array
    {
        return $this->transport->data('DELETE', "/customer/wishlist/{$productId}");
    }
}
