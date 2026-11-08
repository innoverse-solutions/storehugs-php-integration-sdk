<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Http;

use Closure;
use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use stdClass;
use StoreHugs\Integration\IntegrationApiException;

/**
 * The HTTP layer both clients share: JSON in and out, an `Idempotency-Key`
 * on every write (kept across retries, so a retried write never runs twice),
 * retries after rate limits, and coded errors.
 *
 * @internal
 *
 * @phpstan-type Query array<string, scalar|list<int|string>|null>
 */
final class Transport
{
    private const RETRIED_STATUSES = [429, 503];

    private const WRITE_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    private const MAX_RETRY_AFTER_SECONDS = 60;

    private const IN_PROGRESS_DELAY_SECONDS = 1.0;

    private readonly ClientInterface $client;

    private readonly RequestFactoryInterface $requestFactory;

    private readonly StreamFactoryInterface $streamFactory;

    /** @var Closure(float): void */
    private readonly Closure $sleep;

    /**
     * @param  string  $baseUrl  Origin and prefix, e.g. `https://my.storehugs.com/api/admin/v1`.
     * @param  array<string, string|null>  $headers  Sent with every request (keys, tokens).
     */
    public function __construct(
        private readonly string $baseUrl,
        private readonly array $headers,
        private readonly HttpOptions $options,
    ) {
        $this->client = $options->client ?? new Client([
            'connect_timeout' => $options->connectTimeout,
            'timeout' => $options->timeout,
        ]);
        $this->requestFactory = $options->requestFactory ?? new HttpFactory;
        $this->streamFactory = $options->streamFactory ?? new HttpFactory;
        $this->sleep = $options->sleep ?? static function (float $seconds): void {
            usleep((int) round($seconds * 1_000_000));
        };
    }

    /**
     * @param  Query  $query
     * @param  array<array-key, mixed>|null  $body  Sent as JSON; an empty array is `{}`.
     * @param  array<string, string|null>  $headers  Added to the client's; null removes one.
     * @param  string|false|null  $idempotencyKey  Made for writes when null; your own to retry across processes, or false to send none.
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function request(
        string $method,
        string $path,
        array $query = [],
        ?array $body = null,
        array $headers = [],
        string|false|null $idempotencyKey = null,
    ): mixed {
        $isWrite = in_array($method, self::WRITE_METHODS, true);
        $idempotencyKey = $idempotencyKey === false ? null : ($idempotencyKey ?? ($isWrite ? self::uuid() : null));

        $request = $this->requestFactory->createRequest($method, $this->url($path, $query))
            ->withHeader('Accept', 'application/json');

        foreach ([...$this->headers, ...$headers] as $name => $value) {
            if ($value !== null) {
                $request = $request->withHeader($name, $value);
            }
        }

        if ($idempotencyKey !== null) {
            $request = $request->withHeader('Idempotency-Key', $idempotencyKey);
        }

        if ($body !== null) {
            $json = json_encode(
                $body === [] ? new stdClass : $body,
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION,
            );
            $request = $request
                ->withHeader('Content-Type', 'application/json')
                ->withBody($this->streamFactory->createStream($json));
        }

        $retriable = ! $isWrite || $idempotencyKey !== null;
        $startedAt = microtime(true);
        $attempt = 0;

        while (true) {
            try {
                $response = $this->client->sendRequest($request);
            } catch (NetworkExceptionInterface $exception) {
                if (! $retriable || $attempt >= $this->options->maxRetries) {
                    throw $exception;
                }

                ($this->sleep)(self::backoff($attempt++));

                continue;
            }

            $status = $response->getStatusCode();

            if (in_array($status, self::RETRIED_STATUSES, true) && $retriable && $attempt < $this->options->maxRetries) {
                ($this->sleep)(self::retryAfter($response) ?? self::backoff($attempt));
                $attempt++;

                continue;
            }

            $text = $status === 204 ? '' : (string) $response->getBody();
            $decoded = self::decode($text);

            // A retry reached the server while the first attempt still runs:
            // wait for it under the same key, whose answer is then replayed.
            if (
                $idempotencyKey !== null
                && $status === 409
                && microtime(true) - $startedAt < $this->options->inProgressTimeout
                && is_array($decoded)
                && ($decoded['code'] ?? null) === 'idempotency_in_progress'
            ) {
                ($this->sleep)(self::retryAfter($response) ?? self::IN_PROGRESS_DELAY_SECONDS);

                continue;
            }

            if ($status < 200 || $status >= 300) {
                throw new IntegrationApiException($response, $decoded);
            }

            return $decoded;
        }
    }

    /**
     * A request whose answer wraps the record in `data`.
     *
     * @param  Query  $query
     * @param  array<array-key, mixed>|null  $body
     * @param  array<string, string|null>  $headers
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function data(
        string $method,
        string $path,
        array $query = [],
        ?array $body = null,
        array $headers = [],
        string|false|null $idempotencyKey = null,
    ): mixed {
        /** @var array{data: mixed} $answer */
        $answer = $this->request($method, $path, $query, $body, $headers, $idempotencyKey);

        return $answer['data'];
    }

    /**
     * Every item of a cursor-paged list, fetching pages as they are read.
     *
     * @param  Query  $query
     * @return Generator<int, mixed>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function paginate(string $path, array $query = []): Generator
    {
        $cursor = null;

        do {
            /** @var array{data: list<mixed>, meta: array{nextCursor: string|null}} $page */
            $page = $this->request('GET', $path, $cursor === null ? $query : [...$query, 'cursor' => $cursor]);

            yield from $page['data'];

            $cursor = $page['meta']['nextCursor'];
        } while ($cursor !== null);
    }

    /**
     * @param  Query  $query
     */
    private function url(string $path, array $query): string
    {
        $pairs = [];

        foreach ($query as $name => $value) {
            if ($value === null) {
                continue;
            }

            if (is_array($value)) {
                foreach ($value as $item) {
                    $pairs[] = rawurlencode("{$name}[]").'='.rawurlencode((string) $item);
                }

                continue;
            }

            $pairs[] = rawurlencode($name).'='.rawurlencode(is_bool($value) ? ($value ? '1' : '0') : (string) $value);
        }

        return rtrim($this->baseUrl, '/').$path.($pairs === [] ? '' : '?'.implode('&', $pairs));
    }

    private static function decode(string $text): mixed
    {
        if ($text === '') {
            return null;
        }

        $decoded = json_decode($text, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $text;
    }

    private static function retryAfter(ResponseInterface $response): ?float
    {
        $header = $response->getHeaderLine('Retry-After');

        if ($header === '' || ! is_numeric($header) || (float) $header < 0) {
            return null;
        }

        return min((float) $header, self::MAX_RETRY_AFTER_SECONDS);
    }

    private static function backoff(int $attempt): float
    {
        return 0.5 * 2 ** $attempt;
    }

    private static function uuid(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0F) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3F) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
