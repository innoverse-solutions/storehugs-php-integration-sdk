<?php

/*
 * Writes src/Schemas.php from the Integration API's OpenAPI file: a PHPStan
 * type alias for each of its components.schemas, plus a few request and
 * response bodies the spec writes inline. Run `composer generate`.
 *
 * The spec is read from SPEC (a URL or a file path), by default the published
 * one at https://developers.storehugs.com/openapi.yaml.
 */

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

$source = getenv('SPEC') ?: 'https://developers.storehugs.com/openapi.yaml';
$output = __DIR__.'/../src/Schemas.php';

/** Schema names that would clash with the client's own names. */
const RENAMES = [
    'Error' => 'ErrorBody',
    'Page' => 'StorefrontPage',
    'CursorPage' => 'CursorPageEnvelope',
    'NumberedPage' => 'NumberedPageEnvelope',
];

/** Inline request bodies worth a name, by operationId. */
const BODIES = [
    'addCartItem' => 'CartItemInput',
    'checkoutCart' => 'CheckoutOptions',
    'adminUpdateOrderStatus' => 'OrderStatusInput',
    'adminCreateOrderShipment' => 'OrderShipmentInput',
    'adminCreateCustomer' => 'AdminCustomerInput',
    'adminCreateWebhook' => 'WebhookEndpointInput',
    'adminSendQuotation' => 'QuotationSendInput',
];

/** Inline responses worth a name, by operationId: [status, name]. */
const RESPONSES = [
    'adminCreateKey' => ['201', 'CreatedApiKey'],
    'adminCreateWebhook' => ['201', 'CreatedWebhookEndpoint'],
];

/** Shared responses worth a name: component name => type name. */
const COMPONENT_RESPONSES = [
    'AdminProductWritten' => 'ProductWriteResult',
    'AdminQuotationActionDone' => 'QuotationActionResult',
    'AdminTaxonomyTermWritten' => 'TaxonomyTermWriteResult',
    'AdminVariableProductWritten' => 'VariableProductWriteResult',
];

/** Page envelope parts, named so a page of any item can be typed. */
const ENVELOPE_PARTS = [
    'CursorPage' => ['links' => 'CursorPageLinks', 'meta' => 'CursorPageMeta'],
    'NumberedPage' => ['links' => 'NumberedPageLinks', 'meta' => 'NumberedPageMeta'],
];

const INDENT = '    ';

$text = @file_get_contents($source);

if ($text === false) {
    fwrite(STDERR, "Could not read {$source}.\n");
    exit(1);
}

/** @var array<string, mixed> $spec */
$spec = Yaml::parse($text);
$components = $spec['components']['schemas'];
$schemas = $components;

foreach (COMPONENT_RESPONSES as $response => $name) {
    $schemas[$name] = $spec['components']['responses'][$response]['content']['application/json']['schema'];
}

foreach (ENVELOPE_PARTS as $envelope => $parts) {
    foreach ($parts as $property => $name) {
        $schemas[$name] = $components[$envelope]['properties'][$property];
    }
}

foreach ($spec['paths'] as $item) {
    foreach ($item as $operation) {
        if (! is_array($operation) || ! isset($operation['operationId'])) {
            continue;
        }

        $id = $operation['operationId'];

        if (isset(BODIES[$id])) {
            $schemas[BODIES[$id]] = $operation['requestBody']['content']['application/json']['schema'];
        }

        if (isset(RESPONSES[$id])) {
            [$status, $name] = RESPONSES[$id];
            $schemas[$name] = $operation['responses'][$status]['content']['application/json']['schema'];
        }
    }
}

function nameOf(string $ref): string
{
    $name = substr($ref, strrpos($ref, '/') + 1);

    return RENAMES[$name] ?? $name;
}

function literal(mixed $value): string
{
    return match (true) {
        is_string($value) => "'".str_replace(['\\', "'"], ['\\\\', "\\'"], $value)."'",
        is_bool($value) => $value ? 'true' : 'false',
        $value === null => 'null',
        default => (string) $value,
    };
}

/**
 * The properties and required keys of an object schema, following `$ref`
 * and `allOf`; null when a part isn't a plain object.
 *
 * @return array{properties: array<string, mixed>, required: list<string>, nullable: bool, open: bool}|null
 */
function objectParts(mixed $schema): ?array
{
    global $components;

    if (! is_array($schema)) {
        return null;
    }

    if (isset($schema['$ref'])) {
        return objectParts($components[substr($schema['$ref'], strrpos($schema['$ref'], '/') + 1)] ?? null);
    }

    if (isset($schema['allOf'])) {
        $merged = ['properties' => [], 'required' => [], 'nullable' => false, 'open' => false];

        foreach ($schema['allOf'] as $part) {
            $parts = objectParts($part);

            if ($parts === null) {
                return null;
            }

            $merged['properties'] = [...$merged['properties'], ...$parts['properties']];
            $merged['required'] = array_values(array_unique([...$merged['required'], ...$parts['required']]));
            $merged['open'] = $merged['open'] || $parts['open'];
        }

        return $merged;
    }

    $types = (array) ($schema['type'] ?? (isset($schema['properties']) ? 'object' : []));

    if (array_diff($types, ['object', 'null']) !== [] || ! in_array('object', $types, true) || isset($schema['oneOf']) || isset($schema['anyOf'])) {
        return null;
    }

    return [
        'properties' => $schema['properties'] ?? [],
        'required' => $schema['required'] ?? [],
        'nullable' => in_array('null', $types, true),
        'open' => is_array($schema['additionalProperties'] ?? null),
    ];
}

function phpType(mixed $schema, int $indent = 0): string
{
    if (! is_array($schema) || $schema === []) {
        return 'mixed';
    }

    if (isset($schema['$ref'])) {
        global $current;

        // PHPStan can't resolve a type alias that names itself.
        return nameOf($schema['$ref']) === $current ? 'array<string, mixed>' : nameOf($schema['$ref']);
    }

    if (isset($schema['allOf'])) {
        $parts = objectParts($schema);

        return $parts === null ? 'array<string, mixed>' : shape($parts, $indent);
    }

    foreach (['oneOf', 'anyOf'] as $key) {
        if (isset($schema[$key])) {
            return implode('|', array_map(fn (mixed $part): string => phpType($part, $indent), $schema[$key]));
        }
    }

    if (array_key_exists('const', $schema)) {
        return literal($schema['const']);
    }

    $types = (array) ($schema['type'] ?? []);
    $nullable = in_array('null', $types, true);
    $types = array_values(array_filter($types, fn (string $type): bool => $type !== 'null'));

    if (isset($schema['enum'])) {
        $values = array_filter($schema['enum'], fn (mixed $value): bool => $value !== null);
        $out = $values === [] ? 'never' : implode('|', array_map(literal(...), $values));

        return $out.($nullable || in_array(null, $schema['enum'], true) ? '|null' : '');
    }

    if ($types === []) {
        if (isset($schema['properties']) || isset($schema['required'])) {
            $types = ['object'];
        } else {
            return $nullable ? 'null' : 'mixed';
        }
    }

    $outs = array_map(fn (string $type): string => match ($type) {
        'integer' => 'int',
        'number' => 'float|int',
        'string' => 'string',
        'boolean' => 'bool',
        'array' => 'list<'.phpType($schema['items'] ?? null, $indent).'>',
        'object' => objectType($schema, $indent),
        default => 'mixed',
    }, $types);

    return implode('|', $outs).($nullable ? '|null' : '');
}

/**
 * @param  array<string, mixed>  $schema
 */
function objectType(array $schema, int $indent): string
{
    $additional = $schema['additionalProperties'] ?? null;

    if (($schema['properties'] ?? []) === []) {
        if (is_array($additional)) {
            return 'array<string, '.phpType($additional, $indent).'>';
        }

        return $additional === false ? 'array{}' : 'array<string, mixed>';
    }

    return shape([
        'properties' => $schema['properties'],
        'required' => $schema['required'] ?? [],
        'nullable' => false,
        'open' => is_array($additional),
    ], $indent);
}

/**
 * @param  array{properties: array<string, mixed>, required: list<string>, nullable: bool, open: bool}  $parts
 */
function shape(array $parts, int $indent): string
{
    $pad = str_repeat(INDENT, $indent + 1);
    $lines = ['array{'];

    foreach ($parts['properties'] as $key => $value) {
        $key = (string) $key;
        $name = preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $key) === 1 ? $key : literal($key);
        $optional = in_array($key, $parts['required'], true) ? '' : '?';
        $lines[] = "{$pad}{$name}{$optional}: ".phpType($value, $indent + 1).',';
    }

    if ($parts['open']) {
        $lines[] = "{$pad}...";
    }

    $lines[] = str_repeat(INDENT, $indent).'}';

    return implode("\n", $lines).($parts['nullable'] ? '|null' : '');
}

$out = [];
$current = null;

foreach ($schemas as $rawName => $schema) {
    $name = $current = RENAMES[$rawName] ?? $rawName;
    $type = phpType($schema);

    // A top-level nullable object (`type: [object, "null"]`) keeps its null.
    if (is_array($schema) && ! isset($schema['allOf']) && str_starts_with($type, 'array{') && in_array('null', (array) ($schema['type'] ?? []), true) && ! str_ends_with($type, '|null')) {
        $type .= '|null';
    }

    $lines = explode("\n", "@phpstan-type {$name} {$type}");
    $out[] = implode("\n", array_map(fn (string $line): string => rtrim(' * '.$line), $lines));
    $out[] = ' *';
}

array_pop($out);

$php = <<<'PHP'
<?php

declare(strict_types=1);

namespace StoreHugs\Integration;

/**
 * The Integration API's schemas as PHPStan types, generated from its OpenAPI
 * file. Regenerate with `composer generate` rather than editing it; import a
 * type with `@phpstan-import-type Cart from Schemas`.
 *

PHP;

$php .= implode("\n", $out)."\n */\nfinal class Schemas\n{\n    private function __construct() {}\n}\n";

file_put_contents($output, $php);
fwrite(STDOUT, "Wrote src/Schemas.php from {$source}.\n");
