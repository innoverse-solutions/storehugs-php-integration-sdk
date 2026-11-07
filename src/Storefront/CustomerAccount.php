<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Storefront;

use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\Http\Transport;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * The signed-in shopper's own data; needs the client's access token.
 *
 * @phpstan-import-type StorefrontCustomer from Schemas
 */
final class CustomerAccount
{
    public readonly CustomerOrders $orders;

    public readonly CustomerAddresses $addresses;

    public readonly Wishlist $wishlist;

    /** @internal */
    public function __construct(private readonly Transport $transport)
    {
        $this->orders = new CustomerOrders($transport);
        $this->addresses = new CustomerAddresses($transport);
        $this->wishlist = new Wishlist($transport);
    }

    /**
     * @return StorefrontCustomer
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function get(): array
    {
        return $this->transport->data('GET', '/customer');
    }

    /**
     * Ends the access token and its refresh token.
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function logout(): void
    {
        $this->transport->request('POST', '/customer/logout');
    }
}
