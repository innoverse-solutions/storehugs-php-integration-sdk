<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Admin;

use DateTimeInterface;
use Generator;
use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * The units products are sold in, such as piece or kilogram (a product's
 * `unitId`).
 *
 * @phpstan-import-type AdminUnit from Schemas
 * @phpstan-import-type UnitInput from Schemas
 * @phpstan-import-type CursorPageLinks from Schemas
 * @phpstan-import-type CursorPageMeta from Schemas
 *
 * @phpstan-type UnitListQuery array{limit?: int, cursor?: string, updatedSince?: string, search?: string}
 */
final class Units extends Resource
{
    /**
     * One page (`limit` up to 100).
     *
     * @param  UnitListQuery  $query
     * @return array{data: list<AdminUnit>, links: CursorPageLinks, meta: CursorPageMeta}
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function list(array $query = []): array
    {
        return $this->page($query);
    }

    /**
     * Every unit, fetching pages as you read.
     *
     * @param  UnitListQuery  $query
     * @return Generator<int, AdminUnit>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function all(array $query = []): Generator
    {
        return $this->each($query);
    }

    /**
     * @return AdminUnit
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function get(int $id): array
    {
        return $this->find($id);
    }

    /**
     * @param  UnitInput  $input
     * @return AdminUnit
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function create(array $input, string|false|null $idempotencyKey = null): array
    {
        return $this->store($input, $idempotencyKey);
    }

    /**
     * Changes only the fields sent.
     *
     * @param  array<string, mixed>  $changes
     * @return AdminUnit
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function update(int $id, array $changes, DateTimeInterface|string|null $unmodifiedSince = null, string|false|null $idempotencyKey = null): array
    {
        return $this->change('PATCH', "{$this->path}/{$id}", $changes, $unmodifiedSince, $idempotencyKey);
    }

    /**
     * Only a unit no product is sold in can be deleted (else 422).
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function delete(int $id): void
    {
        $this->destroy($id);
    }
}
