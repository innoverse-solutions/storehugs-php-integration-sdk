<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Storefront;

use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\Http\Transport;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * @phpstan-import-type Order from Schemas
 * @phpstan-import-type NumberedPageLinks from Schemas
 * @phpstan-import-type NumberedPageMeta from Schemas
 */
final class CustomerOrders
{
    /** @internal */
    public function __construct(private readonly Transport $transport) {}

    /**
     * @return array{data: list<Order>, links: NumberedPageLinks, meta: NumberedPageMeta}
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function list(?int $page = null): array
    {
        return $this->transport->request('GET', '/customer/orders', ['page' => $page]);
    }

    /**
     * @return Order
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function get(int $id): array
    {
        return $this->transport->data('GET', "/customer/orders/{$id}");
    }
}
