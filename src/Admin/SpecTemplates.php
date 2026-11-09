<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Admin;

use Generator;
use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * The specification templates products can use. Give a product one with
 * `specTemplateId`, and its values in `specs` by property id. Needs a plan
 * that includes them (else 403 `plan_required`).
 *
 * @phpstan-import-type AdminSpecTemplate from Schemas
 * @phpstan-import-type AdminSpecTemplateDetail from Schemas
 * @phpstan-import-type CursorPageLinks from Schemas
 * @phpstan-import-type CursorPageMeta from Schemas
 *
 * @phpstan-type SpecTemplateCategory 'electronics'|'appliances'|'fashion'|'shoes_bags'|'jewelry'|'beauty'|'health'|'home'|'tools_garden'|'baby'|'toys'|'sports'|'automotive'|'pets'|'office'|'grocery'|'media'
 * @phpstan-type SpecTemplateListQuery array{limit?: int, cursor?: string, updatedSince?: string, search?: string, source?: 'mine'|'public', category?: SpecTemplateCategory}
 */
final class SpecTemplates extends Resource
{
    /**
     * One page (`limit` up to 100): the shop's own templates (`source`
     * `mine`, the default) or the ones Store Hugs publishes (`public`),
     * which `category` narrows to one kind of product.
     *
     * @param  SpecTemplateListQuery  $query
     * @return array{data: list<AdminSpecTemplate>, links: CursorPageLinks, meta: CursorPageMeta}
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function list(array $query = []): array
    {
        return $this->page($query);
    }

    /**
     * Every template, fetching pages as you read.
     *
     * @param  SpecTemplateListQuery  $query
     * @return Generator<int, AdminSpecTemplate>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function all(array $query = []): Generator
    {
        return $this->each($query);
    }

    /**
     * One template with its properties, in their groups and order.
     *
     * @return AdminSpecTemplateDetail
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function get(int $id): array
    {
        return $this->find($id);
    }
}
