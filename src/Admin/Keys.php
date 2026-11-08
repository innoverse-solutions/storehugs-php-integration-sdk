<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Admin;

use Generator;
use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * Storefront keys. Admin keys can't be made here.
 *
 * @phpstan-import-type ApiKey from Schemas
 * @phpstan-import-type ApiKeyInput from Schemas
 * @phpstan-import-type CreatedApiKey from Schemas
 * @phpstan-import-type CursorPageLinks from Schemas
 * @phpstan-import-type CursorPageMeta from Schemas
 *
 * @phpstan-type KeyQuery array{limit?: int, cursor?: string, updatedSince?: string}
 */
final class Keys extends Resource
{
    /**
     * One page (`limit` up to 100).
     *
     * @param  KeyQuery  $query
     * @return array{data: list<ApiKey>, links: CursorPageLinks, meta: CursorPageMeta}
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function list(array $query = []): array
    {
        return $this->page($query);
    }

    /**
     * Every key, fetching pages as you read.
     *
     * @param  KeyQuery  $query
     * @return Generator<int, ApiKey>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function all(array $query = []): Generator
    {
        return $this->each($query);
    }

    /**
     * A Storefront key, with its `value`: a secret key's is shown this once.
     *
     * @param  ApiKeyInput  $input
     * @return CreatedApiKey
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function create(array $input, string|false|null $idempotencyKey = null): array
    {
        return $this->transport->request('POST', $this->path, body: $input, idempotencyKey: $idempotencyKey);
    }

    /**
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function revoke(int $id): void
    {
        $this->destroy($id);
    }
}
