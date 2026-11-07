# storehugs/integration

[![Packagist](https://img.shields.io/packagist/v/storehugs/integration)](https://packagist.org/packages/storehugs/integration)
[![CI](https://github.com/innoverse-solutions/storehugs-php-integration-sdk/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/innoverse-solutions/storehugs-php-integration-sdk/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue)](LICENSE)

A PHP client for the Store Hugs Integration API. It runs on PHP 8.2+ and sends requests with Guzzle 7.15.2+ or 8.0.3+, or with any PSR-18 HTTP client you pass it.

- **Storefront API:** the catalog, carts and checkout links, and signed-in shoppers on the shop's host. On a server, use a secret key (`sk_live_…`) and pass the shopper's IP.
- **Admin API:** the merchant's own systems and connected apps. Use an Admin key (`sa_live_…`).

The full reference is at https://developers.storehugs.com (`/openapi.yaml`). Report issues and send changes at https://github.com/innoverse-solutions/storehugs-php-integration-sdk. A TypeScript client with the same API is at https://github.com/innoverse-solutions/storehugs-ts-integration-sdk.

## Install

```bash
composer require storehugs/integration
```

Guzzle comes with it (7.15.2+ or 8.0.3+, whichever your app already uses), so nothing else is needed. Laravel apps already have it. Earlier Guzzle releases have security advisories, and Composer refuses them by default.

## Storefront

```php
use StoreHugs\Integration\StorefrontClient;

$storefront = new StorefrontClient(
    host: 'acme.storehugs.com',
    key: getenv('STOREHUGS_SECRET_KEY'),
    shopperIp: $request->ip(),
);

$products = $storefront->products->list(['search' => 'mug', 'per_page' => 12])['data'];
$cart = $storefront->createCart();
$session = $storefront->cart($cart['cart_token']);
$session->addItem($products[0]['id'], 2);
$link = $session->checkout(['return_path' => '/thanks']);
// Redirect the shopper to $link['checkout_url'].
```

**Shopper sign-in** uses OAuth with PKCE on the shop's own page:

1. Call `Pkce::create()` and `Pkce::state()`, and keep the verifier and state in the session.
2. Send the shopper to `$storefront->signIn->authorizeUrl(…)`.
3. Check the state, then exchange the code with `$storefront->signIn->exchangeCode(…)`.
4. Call `$storefront->withAccessToken($tokens['access_token'])` to act as the shopper.

## Admin

```php
use StoreHugs\Integration\AdminClient;

$admin = new AdminClient(getenv('STOREHUGS_ADMIN_KEY'));

foreach ($admin->orders->all(['updated_since' => '2026-11-01T00:00:00Z']) as $order) {
    // Every page is fetched as you read.
}

$admin->setInventory([['sku' => 'MUG-01', 'stock_quantity' => 40]]);
$product = $admin->products->get(12);
$admin->products->update(12, ['price' => 19.5], unmodifiedSince: $product['updated_at']);
// 409 edit_conflict if it changed
```

**Apps** get an Admin key, not an OAuth token:

1. Send the merchant to `AdminClient::authorizeAppUrl(clientId: …, redirectUri: …, scopes: […], state: …, codeChallenge: …)`.
2. On your server, call `AdminClient::exchangeAppCode(…)` within 10 minutes. It returns `api_key`, `scopes` and `shop`, and the key is shown only that once.

## Webhooks

```php
use StoreHugs\Integration\Webhooks;

$genuine = Webhooks::verifySignature(
    $request->getContent(),
    $request->header(Webhooks::SIGNATURE_HEADER),
    getenv('STOREHUGS_WEBHOOK_SECRET'),
);
```

This checks the HMAC and that the delivery is recent (5 minutes by default). An empty secret throws `InvalidArgumentException` instead of answering, so a missing setting can't let forged deliveries through. Pass the raw body exactly as received. Skip any `Storehugs-Event-Id` you have already handled, because retries reuse it.

## Behaviour

- **Answers are arrays,** decoded from the API's JSON. Their shapes are PHPStan types generated from the OpenAPI file, in `StoreHugs\Integration\Schemas`. Import one with `@phpstan-import-type Cart from Schemas`.
- **Writes are safe to retry.** Every POST, PUT, PATCH and DELETE gets an `Idempotency-Key`. Pass `idempotencyKey: '…'` to keep one across processes, or `idempotencyKey: false` to send none.
- **Retries:** answers 429 and 503, and network errors, are retried up to `maxRetries` times (default 2), following `Retry-After`, with the same key. If a retry reaches the server while the first attempt is still running (409 `idempotency_in_progress`), the client waits for it under the same key for up to `inProgressTimeout` seconds (default 30) and returns its answer.
- **Errors:** a refusal throws `IntegrationApiException`, with `status`, the API's `errorCode` (`insufficient_scope`, `edit_conflict`, …) and, on a 422, `errors` per field. Network errors that outlast the retries are thrown as the HTTP client's own PSR-18 exceptions (with Guzzle, `GuzzleHttp\Exception\ConnectException`).

## HTTP

Every client takes an `HttpOptions`. By default it makes its own Guzzle client, which gives up on a request after 30 seconds (`timeout`) and on connecting after 10 (`connectTimeout`):

```php
use StoreHugs\Integration\AdminClient;
use StoreHugs\Integration\Http\HttpOptions;

$admin = new AdminClient(getenv('STOREHUGS_ADMIN_KEY'), http: new HttpOptions(
    timeout: 60,
    maxRetries: 3,
    inProgressTimeout: 60,
));
```

To use your own HTTP client, pass any PSR-18 client as `client`, for example a Guzzle client with your middleware or Symfony's `Psr18Client`. Its own timeouts then apply. `requestFactory` and `streamFactory` take PSR-17 factories, Guzzle's by default.

## Development

```bash
composer install
composer format:check
composer types:check
composer test
```

`src/Schemas.php` is generated from the Integration API's OpenAPI file. Don't edit it by hand: run `composer generate`, which reads the published spec at `https://developers.storehugs.com/openapi.yaml`. To read another copy, pass a URL or a file path in `SPEC`, for example `SPEC=../matajer/documentation/openapi/integration-api.yaml composer generate`.

A weekly workflow regenerates the types from the published spec and fails when they change, which signals that the client needs a release.

## Releasing

1. Update `CHANGELOG.md` and commit to `master`.
2. Tag the commit and push the tag:

    ```bash
    git tag v0.1.0 && git push origin v0.1.0
    ```

    Packagist reads the version from the tag through the repository's GitHub hook. No version goes in `composer.json`.

CI on pushes and pull requests checks types and tests on PHP 8.2 to 8.5 with Guzzle 8, on PHP 8.5 with the newest Guzzle 7, and on PHP 8.2 with the lowest dependency versions (Guzzle 7.15.2). Formatting is checked once, on PHP 8.5 with the newest Pint.

## Contributing and security

See [CONTRIBUTING.md](CONTRIBUTING.md). Report vulnerabilities privately, as [SECURITY.md](SECURITY.md) describes.

## License

MIT
