<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Admin;

use Generator;
use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * @phpstan-import-type AdminReview from Schemas
 * @phpstan-import-type ReviewStatus from Schemas
 * @phpstan-import-type CursorPageLinks from Schemas
 * @phpstan-import-type CursorPageMeta from Schemas
 *
 * @phpstan-type ReviewListQuery array{limit?: int, cursor?: string, updatedSince?: string, status?: string, rating?: int, productId?: int}
 */
final class Reviews extends Resource
{
    /**
     * One page (`limit` up to 100).
     *
     * @param  ReviewListQuery  $query
     * @return array{data: list<AdminReview>, links: CursorPageLinks, meta: CursorPageMeta}
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function list(array $query = []): array
    {
        return $this->page($query);
    }

    /**
     * Every review, fetching pages as you read.
     *
     * @param  ReviewListQuery  $query
     * @return Generator<int, AdminReview>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function all(array $query = []): Generator
    {
        return $this->each($query);
    }

    /**
     * @return AdminReview
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function get(int $id): array
    {
        return $this->find($id);
    }

    /**
     * @param  ReviewStatus  $status
     * @return AdminReview
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function setStatus(int $id, string $status, string|false|null $idempotencyKey = null): array
    {
        return $this->store(['status' => $status], $idempotencyKey, "/{$id}/status");
    }

    /**
     * @return AdminReview
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function reply(int $id, string $body, string|false|null $idempotencyKey = null): array
    {
        return $this->transport->data('PUT', "{$this->path}/{$id}/reply", body: ['body' => $body], idempotencyKey: $idempotencyKey);
    }

    /**
     * @return AdminReview
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function deleteReply(int $id): array
    {
        return $this->transport->data('DELETE', "{$this->path}/{$id}/reply");
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
