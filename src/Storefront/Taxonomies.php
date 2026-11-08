<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Storefront;

use Psr\Http\Client\ClientExceptionInterface;
use StoreHugs\Integration\Http\Transport;
use StoreHugs\Integration\IntegrationApiException;
use StoreHugs\Integration\Schemas;

/**
 * The shop's classifications (Category, Brand…) and their terms.
 *
 * @phpstan-import-type Taxonomy from Schemas
 * @phpstan-import-type TaxonomyTerm from Schemas
 */
final class Taxonomies
{
    /** @internal */
    public function __construct(private readonly Transport $transport) {}

    /**
     * Every classification, in the panel's order, without its terms.
     *
     * @return list<Taxonomy>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function list(): array
    {
        return $this->transport->data('GET', '/taxonomies');
    }

    /**
     * A classification's terms by its slug, as a tree. A term's id filters
     * `products->list()` (`category`).
     *
     * @return list<TaxonomyTerm>
     *
     * @throws IntegrationApiException
     * @throws ClientExceptionInterface A network error that outlasted the retries.
     */
    public function terms(string $taxonomy): array
    {
        return $this->transport->data('GET', '/taxonomies/'.rawurlencode($taxonomy).'/terms');
    }
}
