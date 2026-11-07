<?php

declare(strict_types=1);

namespace StoreHugs\Integration;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * A refusal from the Integration API: the HTTP status, the API's code
 * (`invalid_api_key`, `insufficient_scope`, `edit_conflict`, …) and, for a
 * 422 validation error, the messages per field. `getCode()` is the status.
 */
final class IntegrationApiException extends RuntimeException
{
    public readonly int $status;

    public readonly ?string $errorCode;

    /** @var array<string, list<string>> */
    public readonly array $errors;

    public function __construct(
        public readonly ResponseInterface $response,
        public readonly mixed $body,
    ) {
        $payload = is_array($body) ? $body : [];
        $this->status = $response->getStatusCode();
        $this->errorCode = is_string($payload['code'] ?? null) ? $payload['code'] : null;
        /** @var array<string, list<string>> $errors */
        $errors = is_array($payload['errors'] ?? null) ? $payload['errors'] : [];
        $this->errors = $errors;

        parent::__construct(
            is_string($payload['message'] ?? null) ? $payload['message'] : "Request failed with status {$this->status}",
            $this->status,
        );
    }

    /** Whether it is a 422 with messages per field. */
    public function isValidationError(): bool
    {
        return $this->status === 422 && $this->errors !== [];
    }
}
