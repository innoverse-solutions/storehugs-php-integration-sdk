<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Tests;

use Closure;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use StoreHugs\Integration\Http\HttpOptions;

/**
 * A PSR-18 client that answers from a queue and records each request.
 */
final class FakeClient implements ClientInterface
{
    /** @var list<RequestInterface> */
    public array $requests = [];

    /** @var list<float> */
    public array $sleeps = [];

    /** @var list<ResponseInterface|Closure(RequestInterface): ResponseInterface> */
    private array $responses;

    /**
     * @param  ResponseInterface|Closure(RequestInterface): ResponseInterface  ...$responses
     */
    public function __construct(ResponseInterface|Closure ...$responses)
    {
        $this->responses = array_values($responses);
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->requests[] = $request;
        $next = array_shift($this->responses);

        if ($next === null) {
            throw new RuntimeException("Unexpected request: {$request->getMethod()} {$request->getUri()}");
        }

        return $next instanceof Closure ? $next($request) : $next;
    }

    public function options(int $maxRetries = 2): HttpOptions
    {
        $factory = new HttpFactory;

        return new HttpOptions($this, $factory, $factory, $maxRetries, sleep: function (float $seconds): void {
            $this->sleeps[] = $seconds;
        });
    }

    public function request(int $index): RequestInterface
    {
        return $this->requests[$index] ?? throw new RuntimeException("No request {$index}.");
    }

    /**
     * @return array<array-key, mixed>|null
     */
    public function body(int $index): ?array
    {
        $body = (string) $this->request($index)->getBody();

        if ($body === '') {
            return null;
        }

        /** @var array<array-key, mixed> */
        return json_decode($body, true, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * @param  array<string, string>  $headers
     */
    public static function json(mixed $body, int $status = 200, array $headers = []): ResponseInterface
    {
        return new Response($status, ['Content-Type' => 'application/json', ...$headers], json_encode($body, JSON_THROW_ON_ERROR));
    }
}
