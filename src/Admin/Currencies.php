<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Admin;

use DateTimeInterface;
use Generator;
use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * The shop's other currencies and their exchange rates, by currency code
 * (a plan that includes multiple currencies, else 403 `plan_required`). A
 * rate is what one unit of the shop's own currency, a page's
 * `meta.baseCurrency`, is worth in it.
 *
 * @phpstan-import-type AdminCurrency from Schemas
 * @phpstan-import-type CurrencyInput from Schemas
 * @phpstan-import-type CursorPageLinks from Schemas
 *
 * @phpstan-type CurrencyListQuery array{limit?: int, cursor?: string, updatedSince?: string}
 * @phpstan-type NewCurrency array{code: string, exchangeRate: float|int, isEnabled?: bool}
 */
final class Currencies extends Resource
{
    /**
     * One page (`limit` up to 100), with the shop's own currency in
     * `meta.baseCurrency`.
     *
     * @param  CurrencyListQuery  $query
     * @return array{data: list<AdminCurrency>, links: CursorPageLinks, meta: array{path: string, perPage: int, nextCursor: string|null, prevCursor: string|null, baseCurrency: string}}
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function list(array $query = []): array
    {
        return $this->page($query);
    }

    /**
     * Every currency, fetching pages as you read.
     *
     * @param  CurrencyListQuery  $query
     * @return Generator<int, AdminCurrency>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function all(array $query = []): Generator
    {
        return $this->each($query);
    }

    /**
     * @param  string  $code  An ISO 4217 code, such as `EUR`.
     * @return AdminCurrency
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function get(string $code): array
    {
        return $this->transport->data('GET', $this->codePath($code));
    }

    /**
     * The shop's own currency, or one it already offers, is refused (422 on
     * `code`).
     *
     * @param  NewCurrency  $input
     * @return AdminCurrency
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function create(array $input, string|false|null $idempotencyKey = null): array
    {
        return $this->store($input, $idempotencyKey);
    }

    /**
     * Changes only the fields sent: the rate, or whether shoppers can pick it.
     *
     * @param  CurrencyInput  $changes
     * @return AdminCurrency
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function update(string $code, array $changes, DateTimeInterface|string|null $unmodifiedSince = null, string|false|null $idempotencyKey = null): array
    {
        return $this->change('PATCH', $this->codePath($code), $changes, $unmodifiedSince, $idempotencyKey);
    }

    /**
     * Stops offering the currency; to hide it for a while, update
     * `isEnabled` instead.
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function delete(string $code): void
    {
        $this->transport->request('DELETE', $this->codePath($code));
    }

    private function codePath(string $code): string
    {
        return "{$this->path}/".rawurlencode($code);
    }
}
