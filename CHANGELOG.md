# Changelog

## 0.1.0 — unreleased

- First release: `StorefrontClient` and `CartSession` for the Storefront API, `AdminClient` for the Admin API.
- Sends requests with Guzzle 7.8+ or 8 (30-second request and 10-second connection timeouts by default), or with any PSR-18 client passed in `HttpOptions`.
- Writes carry an `Idempotency-Key`, kept across retries after 429 and 503 answers and network errors. A retry that finds the first attempt still running waits for it.
- Cursor iteration with `all()`, coded errors (`IntegrationApiException`), PKCE helpers, and webhook signature checks (`Webhooks::verifySignature`).
- Answer shapes as PHPStan types generated from the OpenAPI file (`Schemas`).
- Supports PHP 8.2 and later.
