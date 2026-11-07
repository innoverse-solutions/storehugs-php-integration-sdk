<?php

declare(strict_types=1);

namespace StoreHugs\Integration;

/**
 * PKCE (RFC 7636) for the two sign-in flows: shoppers signing in to the
 * merchant's site, and merchants approving an app for an Admin key. Both
 * require S256. Keep the verifier (in the session) until the code comes back.
 */
final class Pkce
{
    private function __construct() {}

    public static function create(): PkcePair
    {
        $verifier = self::base64Url(random_bytes(32));

        return new PkcePair($verifier, self::base64Url(hash('sha256', $verifier, true)));
    }

    /** A random `state` to send with the redirect and check on the way back. */
    public static function state(): string
    {
        return self::base64Url(random_bytes(16));
    }

    private static function base64Url(string $bytes): string
    {
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }
}
