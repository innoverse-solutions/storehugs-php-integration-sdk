<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Admin;

use DateTimeInterface;
use Generator;
use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * @phpstan-import-type AdminOffer from Schemas
 * @phpstan-import-type OfferInput from Schemas
 * @phpstan-import-type CursorPageLinks from Schemas
 * @phpstan-import-type CursorPageMeta from Schemas
 *
 * @phpstan-type OfferListQuery array{limit?: int, cursor?: string, updated_since?: string}
 */
final class Offers extends Resource
{
    /**
     * One page (`limit` up to 100).
     *
     * @param  OfferListQuery  $query
     * @return array{data: list<AdminOffer>, links: CursorPageLinks, meta: CursorPageMeta}
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function list(array $query = []): array
    {
        return $this->page($query);
    }

    /**
     * Every offer, fetching pages as you read.
     *
     * @param  OfferListQuery  $query
     * @return Generator<int, AdminOffer>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function all(array $query = []): Generator
    {
        return $this->each($query);
    }

    /**
     * @return AdminOffer
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function get(int $id): array
    {
        return $this->find($id);
    }

    /**
     * @param  OfferInput  $input
     * @return AdminOffer
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
     * @return AdminOffer
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function update(int $id, array $changes, DateTimeInterface|string|null $unmodifiedSince = null, string|false|null $idempotencyKey = null): array
    {
        return $this->change('PATCH', "{$this->path}/{$id}", $changes, $unmodifiedSince, $idempotencyKey);
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
