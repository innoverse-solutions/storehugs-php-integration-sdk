<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Admin;

use Generator;
use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * @phpstan-import-type Order from Schemas
 * @phpstan-import-type OrderStatusInput from Schemas
 * @phpstan-import-type OrderShipmentInput from Schemas
 * @phpstan-import-type CursorPageLinks from Schemas
 * @phpstan-import-type CursorPageMeta from Schemas
 *
 * @phpstan-type OrderListQuery array{limit?: int, cursor?: string, updatedSince?: string, status?: string, paymentStatus?: string, source?: string}
 */
final class Orders extends Resource
{
    /**
     * One page (`limit` up to 100).
     *
     * @param  OrderListQuery  $query
     * @return array{data: list<Order>, links: CursorPageLinks, meta: CursorPageMeta}
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function list(array $query = []): array
    {
        return $this->page($query);
    }

    /**
     * Every order, fetching pages as you read.
     *
     * @param  OrderListQuery  $query
     * @return Generator<int, Order>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function all(array $query = []): Generator
    {
        return $this->each($query);
    }

    /**
     * @return Order
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function get(int $id): array
    {
        return $this->find($id);
    }

    /**
     * @param  OrderStatusInput  $input
     * @return Order
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function updateStatus(int $id, array $input, string|false|null $idempotencyKey = null): array
    {
        return $this->store($input, $idempotencyKey, "/{$id}/status");
    }

    /**
     * @param  OrderShipmentInput  $input
     * @return Order
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function recordShipment(int $id, array $input, string|false|null $idempotencyKey = null): array
    {
        return $this->store($input, $idempotencyKey, "/{$id}/shipment");
    }
}
