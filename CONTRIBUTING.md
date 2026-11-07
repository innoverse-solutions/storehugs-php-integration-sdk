# Contributing

Thanks for helping improve `storehugs/integration`. Bug reports, fixes and documentation changes are all welcome.

## Before you start

- **Bugs and ideas:** open an issue first for anything bigger than a small fix, so we can agree on the approach before you write code.
- **Security problems:** don't open a public issue. Follow [SECURITY.md](SECURITY.md).

## Setting up

You need PHP 8.2 or later and Composer.

```bash
git clone git@github.com:innoverse-solutions/storehugs-php-integration-sdk.git
cd storehugs-php-integration-sdk
composer install
```

## Making a change

1. Branch from `master`.
2. Make the change, with a test in `tests/` for any change in behaviour.
3. Run the checks:

    ```bash
    composer format
    composer types:check
    composer test
    ```

4. Add a line to `CHANGELOG.md` under the next version.
5. Open a pull request against `master`. CI runs the same checks on PHP 8.2 to 8.5, with Guzzle 7 and 8.

## Generated types

`src/Schemas.php` is generated from the Integration API's OpenAPI file. Don't edit it by hand. When the API changes, regenerate it:

```bash
composer generate
```

This reads the published spec at `https://developers.storehugs.com/openapi.yaml`. To use a local copy, pass a path in `SPEC`, for example `SPEC=../openapi.yaml composer generate`.

## Releases

Only maintainers publish. A release is a `v*.*.*` tag on `master`; Packagist publishes it from the tag.

## License

By contributing, you agree your contributions are licensed under the [MIT License](LICENSE).
