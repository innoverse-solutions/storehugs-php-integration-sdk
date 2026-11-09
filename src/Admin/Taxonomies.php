<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Admin;

use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * The shop's taxonomies (Category, Brand…), made in the panel, and their
 * terms.
 *
 * @phpstan-import-type AdminTaxonomy from Schemas
 */
final class Taxonomies extends Resource
{
    /**
     * Every taxonomy, in the shop's order, with how many terms it has.
     *
     * @return list<AdminTaxonomy>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function list(): array
    {
        return $this->transport->data('GET', $this->path);
    }

    /**
     * One taxonomy's terms, by the taxonomy's id.
     */
    public function terms(int $taxonomyId): TaxonomyTerms
    {
        return new TaxonomyTerms($this->transport, "{$this->path}/{$taxonomyId}/terms");
    }
}
