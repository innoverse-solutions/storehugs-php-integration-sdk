<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Storefront;

use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\Http\Transport;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * @phpstan-import-type CustomerAddress from Schemas
 * @phpstan-import-type CustomerAddressInput from Schemas
 */
final class CustomerAddresses
{
    /** @internal */
    public function __construct(private readonly Transport $transport) {}

    /**
     * @return list<CustomerAddress>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function list(): array
    {
        return $this->transport->data('GET', '/customer/addresses');
    }

    /**
     * @param  CustomerAddressInput  $input
     * @return CustomerAddress
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function create(array $input, string|false|null $idempotencyKey = null): array
    {
        return $this->transport->data('POST', '/customer/addresses', body: $input, idempotencyKey: $idempotencyKey);
    }

    /**
     * Changes only the fields sent.
     *
     * @param  array<string, mixed>  $changes
     * @return CustomerAddress
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function update(int $id, array $changes, string|false|null $idempotencyKey = null): array
    {
        return $this->transport->data('PATCH', "/customer/addresses/{$id}", body: $changes, idempotencyKey: $idempotencyKey);
    }

    /**
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function delete(int $id): void
    {
        $this->transport->request('DELETE', "/customer/addresses/{$id}");
    }

    /**
     * @return CustomerAddress
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function makeDefault(int $id): array
    {
        return $this->transport->data('POST', "/customer/addresses/{$id}/default");
    }
}
