<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Admin;

use Generator;
use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * Products sold in options such as size or colour. Each variant is a product
 * of its own: change it with `products`, by its `productId`. Needs a plan
 * that includes variable products (else 403 `plan_required`).
 *
 * @phpstan-import-type AdminVariableProduct from Schemas
 * @phpstan-import-type VariableProductInput from Schemas
 * @phpstan-import-type VariableProductWriteResult from Schemas
 * @phpstan-import-type CursorPageLinks from Schemas
 * @phpstan-import-type CursorPageMeta from Schemas
 *
 * @phpstan-type VariableProductListQuery array{limit?: int, cursor?: string, updatedSince?: string, search?: string}
 */
final class VariableProducts extends Resource
{
    /**
     * One page (`limit` up to 100).
     *
     * @param  VariableProductListQuery  $query
     * @return array{data: list<AdminVariableProduct>, links: CursorPageLinks, meta: CursorPageMeta}
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function list(array $query = []): array
    {
        return $this->page($query);
    }

    /**
     * Every variable product, fetching pages as you read.
     *
     * @param  VariableProductListQuery  $query
     * @return Generator<int, AdminVariableProduct>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function all(array $query = []): Generator
    {
        return $this->each($query);
    }

    /**
     * @return AdminVariableProduct
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function get(int $id): array
    {
        return $this->find($id);
    }

    /**
     * With 2 to 50 variants, each giving its `options` as a list of `name`
     * and `value`; option names and values reuse the shop's own. Search
     * first: add variants to one that exists.
     *
     * @param  VariableProductInput  $input
     * @return VariableProductWriteResult
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function create(array $input, string|false|null $idempotencyKey = null): array
    {
        return $this->transport->request('POST', $this->path, body: $input, idempotencyKey: $idempotencyKey);
    }

    /**
     * 1 to 50 variants, each with a value for every option the product has.
     *
     * @param  VariableProductInput  $input
     * @return VariableProductWriteResult
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function addVariants(int $id, array $input, string|false|null $idempotencyKey = null): array
    {
        return $this->transport->request('POST', "{$this->path}/{$id}/variants", body: $input, idempotencyKey: $idempotencyKey);
    }
}
