<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Admin;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Generator;
use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\Http\Transport;
use StoreHugs\Integration\IntegrationApiException;

/**
 * What every Admin API collection shares: cursor pages, and changes that may
 * name the version they change.
 *
 * @internal
 */
abstract class Resource
{
    /** @internal */
    public function __construct(
        protected readonly Transport $transport,
        protected readonly string $path,
    ) {}

    /**
     * @param  array<string, scalar|list<int|string>|null>  $query
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    protected function page(array $query): mixed
    {
        return $this->transport->request('GET', $this->path, $query);
    }

    /**
     * @param  array<string, scalar|list<int|string>|null>  $query
     * @return Generator<int, mixed>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    protected function each(array $query): Generator
    {
        return $this->transport->paginate($this->path, $query);
    }

    /**
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    protected function find(int $id): mixed
    {
        return $this->transport->data('GET', "{$this->path}/{$id}");
    }

    /**
     * @param  array<array-key, mixed>  $input
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    protected function store(array $input, string|false|null $idempotencyKey, string $suffix = ''): mixed
    {
        return $this->transport->data('POST', $this->path.$suffix, body: $input, idempotencyKey: $idempotencyKey);
    }

    /**
     * A change sent only if the record is unchanged since `$unmodifiedSince`
     * (its `updatedAt` as you read it); otherwise 409 `edit_conflict`.
     *
     * @param  array<array-key, mixed>  $changes
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    protected function change(
        string $method,
        string $path,
        array $changes,
        DateTimeInterface|string|null $unmodifiedSince,
        string|false|null $idempotencyKey,
        bool $unwrap = true,
    ): mixed {
        $headers = $unmodifiedSince === null ? [] : ['If-Unmodified-Since' => self::httpDate($unmodifiedSince)];

        return $unwrap
            ? $this->transport->data($method, $path, body: $changes, headers: $headers, idempotencyKey: $idempotencyKey)
            : $this->transport->request($method, $path, body: $changes, headers: $headers, idempotencyKey: $idempotencyKey);
    }

    /**
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    protected function destroy(int $id): void
    {
        $this->transport->request('DELETE', "{$this->path}/{$id}");
    }

    private static function httpDate(DateTimeInterface|string $time): string
    {
        $time = is_string($time) ? new DateTimeImmutable($time) : DateTimeImmutable::createFromInterface($time);

        return $time->setTimezone(new DateTimeZone('UTC'))->format('D, d M Y H:i:s \G\M\T');
    }
}
