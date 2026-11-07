<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Admin;

use DateTimeInterface;
use Generator;
use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * @phpstan-import-type WebhookEndpoint from Schemas
 * @phpstan-import-type WebhookEndpointInput from Schemas
 * @phpstan-import-type CreatedWebhookEndpoint from Schemas
 * @phpstan-import-type CursorPageLinks from Schemas
 * @phpstan-import-type CursorPageMeta from Schemas
 *
 * @phpstan-type WebhookListQuery array{limit?: int, cursor?: string, updated_since?: string}
 */
final class WebhookEndpoints extends Resource
{
    /**
     * One page (`limit` up to 100).
     *
     * @param  WebhookListQuery  $query
     * @return array{data: list<WebhookEndpoint>, links: CursorPageLinks, meta: CursorPageMeta}
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function list(array $query = []): array
    {
        return $this->page($query);
    }

    /**
     * Every endpoint, fetching pages as you read.
     *
     * @param  WebhookListQuery  $query
     * @return Generator<int, WebhookEndpoint>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function all(array $query = []): Generator
    {
        return $this->each($query);
    }

    /**
     * @return WebhookEndpoint
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function get(int $id): array
    {
        return $this->find($id);
    }

    /**
     * The answer's `secret` (`whsec_…`) is shown this once.
     *
     * @param  WebhookEndpointInput  $input
     * @return CreatedWebhookEndpoint
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function create(array $input, string|false|null $idempotencyKey = null): array
    {
        return $this->transport->request('POST', $this->path, body: $input, idempotencyKey: $idempotencyKey);
    }

    /**
     * Changes only the fields sent.
     *
     * @param  array<string, mixed>  $changes
     * @return WebhookEndpoint
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
