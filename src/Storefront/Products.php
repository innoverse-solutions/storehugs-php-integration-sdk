<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Storefront;

use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\Http\Transport;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * The catalog.
 *
 * @phpstan-import-type ProductSummary from Schemas
 * @phpstan-import-type ProductDetail from Schemas
 * @phpstan-import-type NumberedPageLinks from Schemas
 * @phpstan-import-type NumberedPageMeta from Schemas
 *
 * @phpstan-type ProductQuery array{
 *     search?: string,
 *     category?: list<int>,
 *     min_price?: float|int,
 *     max_price?: float|int,
 *     in_stock?: bool,
 *     featured?: bool,
 *     sort?: string,
 *     page?: int,
 *     per_page?: int,
 * }
 */
final class Products
{
    /** @internal */
    public function __construct(private readonly Transport $transport) {}

    /**
     * One page of products. `category` takes term ids; `per_page` is up to 50.
     *
     * @param  ProductQuery  $query
     * @return array{data: list<ProductSummary>, links: NumberedPageLinks, meta: NumberedPageMeta}
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function list(array $query = []): array
    {
        return $this->transport->request('GET', '/products', $query);
    }

    /**
     * @return ProductDetail
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function get(string $slug): array
    {
        return $this->transport->data('GET', '/products/'.rawurlencode($slug));
    }
}
