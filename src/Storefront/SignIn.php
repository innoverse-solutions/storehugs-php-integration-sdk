<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Storefront;

use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\Http\Transport;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * Shopper sign-in on the shop's own page: OAuth authorization code with S256
 * PKCE, one client per publishable key.
 *
 * @phpstan-import-type ShopperTokenResponse from Schemas
 */
final class SignIn
{
    /** @internal */
    public function __construct(
        private readonly Transport $transport,
        private readonly string $origin,
    ) {}

    /** The page to send the shopper to. */
    public function authorizeUrl(string $clientId, string $redirectUri, string $state, string $codeChallenge): string
    {
        return "{$this->origin}/account/authorize?".http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'storefront:customer',
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ], '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * @return ShopperTokenResponse
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function exchangeCode(string $code, string $clientId, string $redirectUri, string $codeVerifier): array
    {
        return $this->token([
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'code_verifier' => $codeVerifier,
        ]);
    }

    /**
     * @return ShopperTokenResponse
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function refresh(string $refreshToken, string $clientId): array
    {
        return $this->token([
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $clientId,
        ]);
    }

    /**
     * @param  array<string, string>  $body
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    private function token(array $body): mixed
    {
        return $this->transport->request('POST', '/oauth/token', body: $body, headers: ['Authorization' => null], idempotencyKey: false);
    }
}
