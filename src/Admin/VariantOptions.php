<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Admin;

use DateTimeInterface;
use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * The options variable products are sold in (Size, Colour…) and their
 * values. Each call answers with the whole option. Needs a plan that
 * includes variable products (else 403 `plan_required`).
 *
 * @phpstan-import-type AdminVariantOption from Schemas
 * @phpstan-import-type VariantOptionInput from Schemas
 * @phpstan-import-type VariantValueInput from Schemas
 */
final class VariantOptions extends Resource
{
    /**
     * Every option with its values; not paginated.
     *
     * @return list<AdminVariantOption>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function list(): array
    {
        return $this->transport->data('GET', $this->path);
    }

    /**
     * A name the shop already has, in any language, is refused.
     *
     * @param  VariantOptionInput  $input
     * @return AdminVariantOption
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function create(array $input, string|false|null $idempotencyKey = null): array
    {
        return $this->store($input, $idempotencyKey);
    }

    /**
     * Changes only the fields sent.
     *
     * @param  array<string, mixed>  $changes
     * @return AdminVariantOption
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function update(int $id, array $changes, DateTimeInterface|string|null $unmodifiedSince = null, string|false|null $idempotencyKey = null): array
    {
        return $this->change('PATCH', "{$this->path}/{$id}", $changes, $unmodifiedSince, $idempotencyKey);
    }

    /**
     * Adds a value (e.g. 5M to Length); a name the option already has is
     * refused.
     *
     * @param  VariantValueInput  $input
     * @return AdminVariantOption
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function createValue(int $optionId, array $input, string|false|null $idempotencyKey = null): array
    {
        return $this->store($input, $idempotencyKey, "/{$optionId}/values");
    }

    /**
     * Changes only the fields sent; an empty `color` removes the swatch.
     *
     * @param  array<string, mixed>  $changes
     * @return AdminVariantOption
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function updateValue(int $optionId, int $valueId, array $changes, DateTimeInterface|string|null $unmodifiedSince = null, string|false|null $idempotencyKey = null): array
    {
        return $this->change('PATCH', "{$this->path}/{$optionId}/values/{$valueId}", $changes, $unmodifiedSince, $idempotencyKey);
    }
}
