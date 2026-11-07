<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Admin;

use DateTimeInterface;
use Generator;
use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * @phpstan-import-type AdminCustomer from Schemas
 * @phpstan-import-type AdminCustomerInput from Schemas
 * @phpstan-import-type CursorPageLinks from Schemas
 * @phpstan-import-type CursorPageMeta from Schemas
 *
 * @phpstan-type CustomerListQuery array{limit?: int, cursor?: string, updated_since?: string, search?: string, email?: string}
 */
final class Customers extends Resource
{
    /**
     * One page (`limit` up to 100).
     *
     * @param  CustomerListQuery  $query
     * @return array{data: list<AdminCustomer>, links: CursorPageLinks, meta: CursorPageMeta}
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function list(array $query = []): array
    {
        return $this->page($query);
    }

    /**
     * Every customer, fetching pages as you read.
     *
     * @param  CustomerListQuery  $query
     * @return Generator<int, AdminCustomer>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function all(array $query = []): Generator
    {
        return $this->each($query);
    }

    /**
     * @return AdminCustomer
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function get(int $id): array
    {
        return $this->find($id);
    }

    /**
     * The customer is emailed at once to choose a password.
     *
     * @param  AdminCustomerInput  $input
     * @return AdminCustomer
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function create(array $input, string|false|null $idempotencyKey = null): array
    {
        return $this->store($input, $idempotencyKey);
    }

    /**
     * A wholesale membership, or null for retail (Business plan).
     *
     * @return AdminCustomer
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function setMembership(int $id, ?int $wholesaleMembershipId, DateTimeInterface|string|null $unmodifiedSince = null, string|false|null $idempotencyKey = null): array
    {
        return $this->change('PUT', "{$this->path}/{$id}/membership", ['wholesale_membership_id' => $wholesaleMembershipId], $unmodifiedSince, $idempotencyKey);
    }
}
