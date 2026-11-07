<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Http;

use Closure;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * How a client sends requests. Without a client, a Guzzle client is made
 * with the timeouts below; pass any PSR-18 client (and PSR-17 factories, if
 * you don't want Guzzle's) to use your own, whose timeouts then apply.
 */
final readonly class HttpOptions
{
    /**
     * @param  int  $maxRetries  Retries after 429 and 503 answers and network errors. Writes are retried with the same `Idempotency-Key`.
     * @param  float  $inProgressTimeout  How long to keep waiting, in seconds, while a retried write still runs on the server (409 `idempotency_in_progress`).
     * @param  (Closure(float): void)|null  $sleep  Waits the given seconds; `usleep` by default.
     * @param  float  $timeout  Seconds a request may take in all, for the default Guzzle client.
     * @param  float  $connectTimeout  Seconds to wait for a connection, for the default Guzzle client.
     */
    public function __construct(
        public ?ClientInterface $client = null,
        public ?RequestFactoryInterface $requestFactory = null,
        public ?StreamFactoryInterface $streamFactory = null,
        public int $maxRetries = 2,
        public float $inProgressTimeout = 30.0,
        public ?Closure $sleep = null,
        public float $timeout = 30.0,
        public float $connectTimeout = 10.0,
    ) {}
}
