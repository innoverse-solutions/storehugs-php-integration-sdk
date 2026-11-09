# Changelog

## Unreleased

- Types regenerated from the current spec. `AdminProduct` gains `relatedProductIds` and `recommendedProductIds` (the product page's You May Also Like picks), which `ProductInput` also takes.

## 0.2.0 — 2026-11-08

- **Breaking:** field names are camelCase everywhere: queries, request bodies, answers and webhook payloads (`perPage`, `updatedSince`, `cartToken`, `checkoutUrl`, `productId`, `updatedAt`, `meta.nextCursor`, `line1`, the envelope's `createdAt`). `all()` follows `meta.nextCursor`, and `addItem()`, `wishlist->add()` and `setMembership()` send `productId` and `wholesaleMembershipId`. The OAuth token answers keep the standard's snake_case (`access_token`, `refresh_token`, `expires_in`, `token_type`, and the app answer's `api_key`); error codes, event types and enum values are unchanged.
- **Breaking:** the product query's `category` is now `taxonomy` (term ids from any classification), with `minPrice`, `maxPrice`, `inStock` and `perPage`.
- **Breaking:** `$storefront->categories()` is gone, with the API's `/categories`. `$storefront->taxonomies->list()` lists the shop's classifications (`Taxonomy`, without terms), and `$storefront->taxonomies->terms($slug)` reads one classification's terms as a tree (`TaxonomyTerm`). The `CategoryType` and `CategoryTerm` types are removed.

## 0.1.0 — unreleased

- First release: `StorefrontClient` and `CartSession` for the Storefront API, `AdminClient` for the Admin API.
- Sends requests with Guzzle 7.15.2+ or 8.0.3+ (30-second request and 10-second connection timeouts by default), or with any PSR-18 client passed in `HttpOptions`.
- Writes carry an `Idempotency-Key`, kept across retries after 429 and 503 answers and network errors. A retry that finds the first attempt still running waits for it.
- Cursor iteration with `all()`, coded errors (`IntegrationApiException`), PKCE helpers, and webhook signature checks (`Webhooks::verifySignature`, which throws on an empty secret).
- Answer shapes as PHPStan types generated from the OpenAPI file (`Schemas`).
- Supports PHP 8.2 and later.
