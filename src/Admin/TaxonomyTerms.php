<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Admin;

use DateTimeInterface;
use Generator;
use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * One taxonomy's terms, such as its categories or brands (a product's
 * `taxonomyTermIds`).
 *
 * @phpstan-import-type AdminTaxonomyTerm from Schemas
 * @phpstan-import-type TaxonomyTermInput from Schemas
 * @phpstan-import-type TaxonomyTermWriteResult from Schemas
 * @phpstan-import-type CursorPageLinks from Schemas
 * @phpstan-import-type CursorPageMeta from Schemas
 *
 * @phpstan-type TaxonomyTermListQuery array{limit?: int, cursor?: string, updatedSince?: string, search?: string, parentId?: int}
 */
final class TaxonomyTerms extends Resource
{
    /**
     * One page (`limit` up to 100). `parentId` lists only the terms directly
     * under a term; `0` lists the top level.
     *
     * @param  TaxonomyTermListQuery  $query
     * @return array{data: list<AdminTaxonomyTerm>, links: CursorPageLinks, meta: CursorPageMeta}
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function list(array $query = []): array
    {
        return $this->page($query);
    }

    /**
     * Every term, fetching pages as you read.
     *
     * @param  TaxonomyTermListQuery  $query
     * @return Generator<int, AdminTaxonomyTerm>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function all(array $query = []): Generator
    {
        return $this->each($query);
    }

    /**
     * @return AdminTaxonomyTerm
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function get(int $id): array
    {
        return $this->find($id);
    }

    /**
     * An image that couldn't be stored is skipped and explained in `warnings`.
     *
     * @param  TaxonomyTermInput  $input
     * @return TaxonomyTermWriteResult
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function create(array $input, string|false|null $idempotencyKey = null): array
    {
        return $this->transport->request('POST', $this->path, body: $input, idempotencyKey: $idempotencyKey);
    }

    /**
     * Changes only the fields sent: `parentId` null moves it to the top
     * level, `removeImage` removes its image. The slug stays.
     *
     * @param  array<string, mixed>  $changes
     * @return TaxonomyTermWriteResult
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function update(int $id, array $changes, DateTimeInterface|string|null $unmodifiedSince = null, string|false|null $idempotencyKey = null): array
    {
        return $this->change('PATCH', "{$this->path}/{$id}", $changes, $unmodifiedSince, $idempotencyKey, unwrap: false);
    }

    /**
     * Its products leave it and the terms under it move to the top level.
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function delete(int $id): void
    {
        $this->destroy($id);
    }
}
