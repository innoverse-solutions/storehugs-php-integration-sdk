<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Admin;

use DateTimeInterface;
use Generator;
use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * @phpstan-import-type AdminQuotation from Schemas
 * @phpstan-import-type QuotationInput from Schemas
 * @phpstan-import-type QuotationSendInput from Schemas
 * @phpstan-import-type QuotationActionResult from Schemas
 * @phpstan-import-type CursorPageLinks from Schemas
 * @phpstan-import-type CursorPageMeta from Schemas
 *
 * @phpstan-type QuotationListQuery array{limit?: int, cursor?: string, updated_since?: string, status?: string, customer_id?: int, quotation_number?: string}
 */
final class Quotations extends Resource
{
    /**
     * One page (`limit` up to 100).
     *
     * @param  QuotationListQuery  $query
     * @return array{data: list<AdminQuotation>, links: CursorPageLinks, meta: CursorPageMeta}
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function list(array $query = []): array
    {
        return $this->page($query);
    }

    /**
     * Every quotation, fetching pages as you read.
     *
     * @param  QuotationListQuery  $query
     * @return Generator<int, AdminQuotation>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function all(array $query = []): Generator
    {
        return $this->each($query);
    }

    /**
     * @return AdminQuotation
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function get(int $id): array
    {
        return $this->find($id);
    }

    /**
     * A draft; nothing is emailed.
     *
     * @param  QuotationInput  $input
     * @return AdminQuotation
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function create(array $input, string|false|null $idempotencyKey = null): array
    {
        return $this->store($input, $idempotencyKey);
    }

    /**
     * A draft only; `items` replaces every line.
     *
     * @param  array<string, mixed>  $changes
     * @return AdminQuotation
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function update(int $id, array $changes, DateTimeInterface|string|null $unmodifiedSince = null, string|false|null $idempotencyKey = null): array
    {
        return $this->change('PATCH', "{$this->path}/{$id}", $changes, $unmodifiedSince, $idempotencyKey);
    }

    /**
     * Emails the PDF; a draft is published first.
     *
     * @param  QuotationSendInput  $input
     * @return AdminQuotation
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function send(int $id, array $input = [], string|false|null $idempotencyKey = null): array
    {
        return $this->store($input, $idempotencyKey, "/{$id}/send");
    }

    /**
     * `Revise` answers with the new draft; `AgreeAndOrder` and `CreateOrder` with the order made.
     *
     * @return QuotationActionResult
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function perform(int $id, QuotationAction $action, ?string $note = null, string|false|null $idempotencyKey = null): array
    {
        return $this->transport->request(
            'POST',
            "{$this->path}/{$id}/{$action->value}",
            body: $note === null ? [] : ['note' => $note],
            idempotencyKey: $idempotencyKey,
        );
    }
}
