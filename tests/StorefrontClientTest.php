<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Tests;

use PHPUnit\Framework\TestCase;
use StoreHugs\Integration\StorefrontClient;

final class StorefrontClientTest extends TestCase
{
    public function test_catalog_calls_send_the_key_locale_and_laravel_style_arrays(): void
    {
        $client = new FakeClient(FakeClient::json(['data' => [], 'links' => [], 'meta' => []]));
        $storefront = new StorefrontClient('acme.storehugs.com', 'pk_live_x', locale: 'ar', http: $client->options());

        $storefront->products->list(['taxonomy' => [3, 4], 'inStock' => true, 'perPage' => 20]);

        $this->assertSame(
            'https://acme.storehugs.com/api/v1/products?taxonomy%5B%5D=3&taxonomy%5B%5D=4&inStock=1&perPage=20',
            (string) $client->request(0)->getUri(),
        );
        $this->assertSame('pk_live_x', $client->request(0)->getHeaderLine('X-Storehugs-Key'));
        $this->assertSame('ar', $client->request(0)->getHeaderLine('Accept-Language'));
        $this->assertFalse($client->request(0)->hasHeader('Authorization'));
    }

    public function test_a_cart_session_sends_its_token_and_a_secret_key_names_the_shopper(): void
    {
        $client = new FakeClient(
            FakeClient::json(['data' => ['cartToken' => 't1']], 201),
            FakeClient::json(['data' => ['cartToken' => 't1']]),
        );
        $storefront = new StorefrontClient('https://shop.example.com/', 'sk_live_x', shopperIp: '203.0.113.7', http: $client->options());

        $cart = $storefront->createCart();
        $storefront->cart($cart['cartToken'])->addItem(12, 2);

        $this->assertSame('https://shop.example.com/api/v1/cart/items', (string) $client->request(1)->getUri());
        $this->assertSame('t1', $client->request(1)->getHeaderLine('X-Cart-Token'));
        $this->assertSame('203.0.113.7', $client->request(1)->getHeaderLine('X-Shopper-Ip'));
        $this->assertSame(['productId' => 12, 'quantity' => 2], $client->body(1));
    }

    public function test_checkout_without_options_sends_an_empty_object(): void
    {
        $client = new FakeClient(FakeClient::json(['data' => ['checkoutUrl' => 'https://acme.storehugs.com/checkout/x']]));
        $storefront = new StorefrontClient('acme.storehugs.com', 'pk_live_x', http: $client->options());

        $link = $storefront->cart('t1')->checkout();

        $this->assertSame('https://acme.storehugs.com/checkout/x', $link['checkoutUrl']);
        $this->assertSame('{}', (string) $client->request(0)->getBody());
    }

    public function test_a_signed_in_shoppers_calls_carry_their_token(): void
    {
        $client = new FakeClient(FakeClient::json(['data' => ['id' => 4]]));
        $storefront = (new StorefrontClient('acme.storehugs.com', 'pk_live_x', http: $client->options()))->withAccessToken('at');

        $storefront->customer->get();

        $this->assertSame('Bearer at', $client->request(0)->getHeaderLine('Authorization'));
        $this->assertSame('pk_live_x', $client->request(0)->getHeaderLine('X-Storehugs-Key'));
    }

    public function test_sign_in_sends_the_shopper_to_the_shops_page_and_trades_the_code_for_tokens(): void
    {
        $client = new FakeClient(FakeClient::json(['access_token' => 'at', 'refresh_token' => 'rt', 'expires_in' => 3600, 'token_type' => 'Bearer']));
        $storefront = (new StorefrontClient('acme.storehugs.com', 'pk_live_x', http: $client->options()))->withAccessToken('old');

        $url = $storefront->signIn->authorizeUrl('c1', 'https://www.brand.com/cb', 's', 'ch');
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
        $tokens = $storefront->signIn->exchangeCode('code', 'c1', 'https://www.brand.com/cb', 'v');

        $this->assertStringStartsWith('https://acme.storehugs.com/account/authorize?', $url);
        $this->assertSame('storefront:customer', $query['scope']);
        $this->assertSame('https://www.brand.com/cb', $query['redirect_uri']);
        $this->assertSame('at', $tokens['access_token']);
        $this->assertSame('https://acme.storehugs.com/api/v1/oauth/token', (string) $client->request(0)->getUri());
        $this->assertFalse($client->request(0)->hasHeader('Authorization'));
        $this->assertFalse($client->request(0)->hasHeader('Idempotency-Key'));
    }

    public function test_removing_from_the_wishlist_answers_with_the_whole_wishlist(): void
    {
        $client = new FakeClient(FakeClient::json(['data' => [['id' => 3]]]));
        $storefront = (new StorefrontClient('acme.storehugs.com', 'pk_live_x', http: $client->options()))->withAccessToken('at');

        $wishlist = $storefront->customer->wishlist->remove(9);

        $this->assertSame('DELETE', $client->request(0)->getMethod());
        $this->assertSame('https://acme.storehugs.com/api/v1/customer/wishlist/9', (string) $client->request(0)->getUri());
        $this->assertSame([3], array_column($wishlist, 'id'));
    }

    public function test_a_product_slug_is_encoded_in_the_path(): void
    {
        $client = new FakeClient(FakeClient::json(['data' => ['id' => 1]]));
        $storefront = new StorefrontClient('acme.storehugs.com', 'pk_live_x', http: $client->options());

        $storefront->products->get('mug/../x');

        $this->assertSame('https://acme.storehugs.com/api/v1/products/mug%2F..%2Fx', (string) $client->request(0)->getUri());
    }

    public function test_classifications_are_listed_and_a_classification_names_its_terms_by_slug(): void
    {
        $client = new FakeClient(
            FakeClient::json(['data' => [['id' => 1, 'slug' => 'brand', 'name' => 'Brand', 'isTree' => false, 'isMultiple' => false]]]),
            FakeClient::json(['data' => [['id' => 7, 'slug' => 'acme', 'name' => 'Acme', 'children' => []]]]),
        );
        $storefront = new StorefrontClient('acme.storehugs.com', 'pk_live_x', http: $client->options());

        $brand = $storefront->taxonomies->list()[0];
        $terms = $storefront->taxonomies->terms($brand['slug']);

        $this->assertSame('https://acme.storehugs.com/api/v1/taxonomies', (string) $client->request(0)->getUri());
        $this->assertSame('https://acme.storehugs.com/api/v1/taxonomies/brand/terms', (string) $client->request(1)->getUri());
        $this->assertSame(7, $terms[0]['id']);
    }
}
