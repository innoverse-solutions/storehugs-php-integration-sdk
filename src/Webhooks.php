<?php

declare(strict_types=1);

namespace StoreHugs\Integration;

/**
 * Checks webhook deliveries. Every delivery carries the headers below.
 */
final class Webhooks
{
    public const EVENT_ID_HEADER = 'Storehugs-Event-Id';

    public const EVENT_TYPE_HEADER = 'Storehugs-Event-Type';

    public const SIGNATURE_HEADER = 'Storehugs-Signature';

    private function __construct() {}

    /**
     * Whether a delivery is genuine: an HMAC-SHA256 of `"{t}.{raw body}"` with
     * the endpoint's `whsec_…` secret matches one of the header's `v1` values
     * (two while a replaced secret still signs), and `t` is recent. Pass the
     * raw body exactly as received; re-encoded JSON won't match. Still skip a
     * `Storehugs-Event-Id` you already handled: retries reuse it.
     *
     * @param  int  $toleranceSeconds  How far the signed time may be from now.
     * @param  int|null  $now  The current time in seconds, for tests.
     */
    public static function verifySignature(
        string $rawBody,
        ?string $signatureHeader,
        string $secret,
        int $toleranceSeconds = 300,
        ?int $now = null,
    ): bool {
        if ($signatureHeader === null || $signatureHeader === '') {
            return false;
        }

        $timestamp = null;
        $signatures = [];

        foreach (explode(',', $signatureHeader) as $part) {
            [$key, $value] = array_pad(explode('=', trim($part), 2), 2, '');

            if ($key === 't' && preg_match('/^\d+$/', $value) === 1) {
                $timestamp = (int) $value;
            } elseif ($key === 'v1' && $value !== '') {
                $signatures[] = strtolower($value);
            }
        }

        if ($timestamp === null || abs(($now ?? time()) - $timestamp) > $toleranceSeconds) {
            return false;
        }

        $expected = hash_hmac('sha256', "{$timestamp}.{$rawBody}", $secret);

        foreach ($signatures as $signature) {
            if (hash_equals($expected, $signature)) {
                return true;
            }
        }

        return false;
    }
}
