<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Admin;

use DateTimeInterface;
use Generator;
use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * @phpstan-import-type AdminProduct from Schemas
 * @phpstan-import-type ProductInput from Schemas
 * @phpstan-import-type ProductWriteResult from Schemas
 * @phpstan-import-type CursorPageLinks from Schemas
 * @phpstan-import-type CursorPageMeta from Schemas
 *
 * @phpstan-type ProductListQuery array{limit?: int, cursor?: string, updatedSince?: string, search?: string, sku?: string, status?: string}
 */
final class Products extends Resource
{
    /**
     * One page (`limit` up to 100).
     *
     * @param  ProductListQuery  $query
     * @return array{data: list<AdminProduct>, links: CursorPageLinks, meta: CursorPageMeta}
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function list(array $query = []): array
    {
        return $this->page($query);
    }

    /**
     * Every product, fetching pages as you read.
     *
     * @param  ProductListQuery  $query
     * @return Generator<int, AdminProduct>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function all(array $query = []): Generator
    {
        return $this->each($query);
    }

    /**
     * @return AdminProduct
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function get(int $id): array
    {
        return $this->find($id);
    }

    /**
     * Images that couldn't be stored are skipped and listed in `warnings`.
     *
     * @param  ProductInput  $input
     * @return ProductWriteResult
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function create(array $input, string|false|null $idempotencyKey = null): array
    {
        return $this->transport->request('POST', $this->path, body: $input, idempotencyKey: $idempotencyKey);
    }

    /**
     * Changes only the fields sent; images are added unless `imageMode` is `replace`.
     *
     * @param  array<string, mixed>  $changes
     * @return ProductWriteResult
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function update(int $id, array $changes, DateTimeInterface|string|null $unmodifiedSince = null, string|false|null $idempotencyKey = null): array
    {
        return $this->change('PATCH', "{$this->path}/{$id}", $changes, $unmodifiedSince, $idempotencyKey, unwrap: false);
    }

    /**
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function delete(int $id): void
    {
        $this->destroy($id);
    }
}
