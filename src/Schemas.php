<?php

declare(strict_types=1);

namespace StoreHugs\Integration;

/**
 * The Integration API's schemas as PHPStan types, generated from its OpenAPI
 * file. Regenerate with `composer generate` rather than editing it; import a
 * type with `@phpstan-import-type Cart from Schemas`.
 *
 * @phpstan-type ErrorBody array{
 *     message: string,
 *     code?: string,
 * }
 *
 * @phpstan-type ValidationError array{
 *     message: string,
 *     errors: array<string, list<string>>,
 * }
 *
 * @phpstan-type OAuthError array{
 *     error: string,
 *     error_description?: string,
 *     hint?: string,
 *     message?: string,
 * }
 *
 * @phpstan-type Money array{
 *     amount: float|int,
 *     currency: string,
 *     formatted: string,
 * }
 *
 * @phpstan-type UpdatedAtGuard string|null
 *
 * @phpstan-type CursorPageEnvelope array{
 *     links: array{
 *         first: null,
 *         last: null,
 *         prev: string|null,
 *         next: string|null,
 *     },
 *     meta: array{
 *         path: string,
 *         per_page: int,
 *         next_cursor: string|null,
 *         prev_cursor: string|null,
 *     },
 * }
 *
 * @phpstan-type NumberedPageEnvelope array{
 *     links: array{
 *         first: string,
 *         last: string,
 *         prev: string|null,
 *         next: string|null,
 *     },
 *     meta: array{
 *         current_page: int,
 *         from: int|null,
 *         last_page: int,
 *         links: list<array{
 *             url: string|null,
 *             label: string,
 *             page?: int|null,
 *             active: bool,
 *         }>,
 *         path: string,
 *         per_page: int,
 *         to: int|null,
 *         total: int,
 *     },
 * }
 *
 * @phpstan-type OrderStatus 'unconfirmed'|'pending'|'processing'|'ready_to_ship'|'shipped'|'completed'|'cancelled'
 *
 * @phpstan-type PaymentStatus 'unpaid'|'pending'|'paid'|'failed'|'refunded'|'disputed'
 *
 * @phpstan-type OrderSource 'checkout'|'whatsapp'|'quotation'|'enquiry'
 *
 * @phpstan-type QuotationStatus 'draft'|'published'|'agreed'|'superseded'|'cancelled'
 *
 * @phpstan-type EnquiryStatus 'new'|'quoted'|'agreed'|'completed'|'cancelled'
 *
 * @phpstan-type ReviewStatus 'pending_approval'|'published'|'rejected'|'flagged'
 *
 * @phpstan-type AdminApiScope 'shop:read'|'keys:read'|'keys:write'|'products:read'|'products:write'|'inventory:write'|'orders:read'|'orders:write'|'customers:read'|'customers:write'|'marketing:read'|'marketing:write'|'reviews:read'|'reviews:write'|'quotations:read'|'quotations:write'|'webhooks:read'|'webhooks:write'
 *
 * @phpstan-type SubscribableWebhookEvent 'order.created'|'order.status_changed'|'order.payment_status_changed'|'order.refunded'|'product.created'|'product.updated'|'product.deleted'|'product.stock_changed'|'catalog.changed'|'customer.created'|'customer.updated'|'quotation.created'|'quotation.status_changed'|'enquiry.created'|'enquiry.status_changed'
 *
 * @phpstan-type WebhookEvent 'order.created'|'order.status_changed'|'order.payment_status_changed'|'order.refunded'|'product.created'|'product.updated'|'product.deleted'|'product.stock_changed'|'catalog.changed'|'customer.created'|'customer.updated'|'quotation.created'|'quotation.status_changed'|'enquiry.created'|'enquiry.status_changed'|'ping'
 *
 * @phpstan-type StorefrontShop array{
 *     name: string,
 *     logo: string|null,
 *     locale: string,
 *     default_language: string,
 *     languages: list<string>,
 *     currency: string,
 *     prices_include_tax: bool,
 *     payment_methods: list<array{
 *         id: string,
 *         label: string,
 *     }>,
 *     cart_available: bool,
 *     shipping_estimates: bool,
 *     site_url: string|null,
 * }
 *
 * @phpstan-type Rating array{
 *     average: float|int,
 *     count: int,
 * }|null
 *
 * @phpstan-type ProductSummary array{
 *     id: int,
 *     slug: string,
 *     title: string|null,
 *     sku: string|null,
 *     price: Money,
 *     regular_price: Money,
 *     on_sale: bool,
 *     in_stock: bool,
 *     rating: Rating,
 *     image: string|null,
 * }
 *
 * @phpstan-type ProductDetail array{
 *     id: int,
 *     slug: string,
 *     title: string|null,
 *     sku: string|null,
 *     price: Money,
 *     regular_price: Money,
 *     on_sale: bool,
 *     in_stock: bool,
 *     rating: Rating,
 *     image: string|null,
 *     description: string|null,
 *     short_description: string|null,
 *     part_number: string|null,
 *     unit: string|null,
 *     stock_quantity: int|null,
 *     images: list<string>,
 *     categories: list<array{
 *         id: int,
 *         slug: string,
 *         name: string|null,
 *         type: string|null,
 *     }>,
 *     specifications: list<array{
 *         group: string|null,
 *         rows: list<array{
 *             name: string,
 *             value: string,
 *             unit: string|null,
 *         }>,
 *     }>,
 *     reviews: array{
 *         average_rating: float|int,
 *         total_reviews: int,
 *         distribution: array{
 *             '5': int,
 *             '4': int,
 *             '3': int,
 *             '2': int,
 *             '1': int,
 *         },
 *     }|null,
 *     variants: list<array{
 *         product_id: int,
 *         slug: string,
 *         is_this_product: bool,
 *         options: list<array{
 *             type: string|null,
 *             value: string|null,
 *         }>,
 *         price: Money,
 *         in_stock: bool,
 *     }>,
 * }
 *
 * @phpstan-type CategoryType array{
 *     id: int,
 *     slug: string,
 *     name: string|null,
 *     terms: list<CategoryTerm>,
 * }
 *
 * @phpstan-type CategoryTerm array{
 *     id: int,
 *     slug: string,
 *     name: string|null,
 *     children: list<array<string, mixed>>,
 * }
 *
 * @phpstan-type StorefrontPage array{
 *     slug: 'about'|'contact-us'|'terms-conditions'|'privacy-policy',
 *     title: string|null,
 *     content: string|null,
 * }
 *
 * @phpstan-type Cart array{
 *     cart_token: string,
 *     mode: 'checkout'|'enquiry',
 *     customer_id: int|null,
 *     items: list<CartItem>,
 *     total_quantity: int,
 *     subtotal: Money,
 *     discount: Money,
 *     coupon: array{
 *         code: string,
 *         auto_applied: bool,
 *     }|null,
 *     coupon_error: string|null,
 *     offers: list<string>,
 *     tax_added: Money,
 *     tax_included: Money,
 *     total_before_shipping: Money,
 * }
 *
 * @phpstan-type CartItem array{
 *     id: int,
 *     product_id: int,
 *     slug: string|null,
 *     title: string|null,
 *     image: string|null,
 *     quantity: int,
 *     unit_price: Money,
 *     line_total: Money,
 * }
 *
 * @phpstan-type ShippingDestination array{
 *     shipping_country_code: string,
 *     shipping_city?: string|null,
 *     shipping_state?: string|null,
 *     shipping_postal_code?: string|null,
 *     shipping_address_line_1?: string|null,
 *     shipping_state_id?: int|null,
 *     shipping_city_id?: int|null,
 *     shipping_neighbourhood_id?: int|null,
 *     shipping_location_unlisted?: bool|null,
 * }
 *
 * @phpstan-type ShippingEstimate array{
 *     ships_to: bool,
 *     ships_free: bool,
 *     quotes: list<array{
 *         method_id: int,
 *         name: string,
 *         cost: float|int,
 *         is_live_rate: bool,
 *         is_carrier_price: bool,
 *     }>,
 * }
 *
 * @phpstan-type CheckoutLink array{
 *     checkout_url: string,
 *     expires_at: string,
 *     mode: 'checkout'|'enquiry',
 *     requires_sign_in: bool,
 * }
 *
 * @phpstan-type ShopperTokenRequest array{
 *     grant_type: 'authorization_code',
 *     client_id: string,
 *     redirect_uri: string,
 *     code_verifier: string,
 *     code: string,
 * }|array{
 *     grant_type: 'refresh_token',
 *     client_id: string,
 *     refresh_token: string,
 * }
 *
 * @phpstan-type ShopperTokenResponse array{
 *     token_type: 'Bearer',
 *     expires_in: int,
 *     access_token: string,
 *     refresh_token: string,
 * }
 *
 * @phpstan-type StorefrontCustomer array{
 *     id: int,
 *     name: string,
 *     email: string|null,
 *     email_verified: bool,
 *     phone: string|null,
 *     wholesale: array{
 *         name: string,
 *     }|null,
 *     account_url: string,
 * }
 *
 * @phpstan-type CustomerAddress array{
 *     id: int,
 *     label: string|null,
 *     name: string,
 *     phone: string,
 *     country_code: string,
 *     state: string|null,
 *     state_id: int|null,
 *     city: string|null,
 *     city_id: int|null,
 *     neighbourhood: string|null,
 *     neighbourhood_id: int|null,
 *     address_line_1: string,
 *     address_line_2: string|null,
 *     postal_code: string|null,
 *     is_default: bool,
 * }
 *
 * @phpstan-type CustomerAddressInput array{
 *     label?: string|null,
 *     name: string,
 *     phone: string,
 *     country_code: string,
 *     city: string,
 *     state?: string|null,
 *     address_line_1: string,
 *     address_line_2?: string|null,
 *     postal_code?: string|null,
 *     is_default?: bool,
 *     shipping_state_id?: int|null,
 *     shipping_city_id?: int|null,
 *     shipping_neighbourhood_id?: int|null,
 *     shipping_location_unlisted?: bool|null,
 * }
 *
 * @phpstan-type Order array{
 *     id: int,
 *     order_number: string,
 *     status: OrderStatus,
 *     payment_status: PaymentStatus|null,
 *     payment_method: string|null,
 *     source: string,
 *     placed_at: string|null,
 *     paid_at: string|null,
 *     customer: array{
 *         id: int|null,
 *         name: string|null,
 *         email: string|null,
 *         phone: string|null,
 *     },
 *     shipping_address: array{
 *         line_1: string|null,
 *         line_2: string|null,
 *         neighbourhood: string|null,
 *         city: string|null,
 *         state: string|null,
 *         postal_code: string|null,
 *         country_code: string|null,
 *     },
 *     billing_address: array{
 *         name: string|null,
 *         phone: string|null,
 *         line_1: string|null,
 *         line_2: string|null,
 *         city: string|null,
 *         state: string|null,
 *         postal_code: string|null,
 *         country_code: string|null,
 *     }|null,
 *     shipping_method: string|null,
 *     notes: string|null,
 *     items: list<array{
 *         product_id: int|null,
 *         title: string|null,
 *         sku: string|null,
 *         quantity: int,
 *         unit_price: Money,
 *         total: Money,
 *     }>,
 *     subtotal: Money,
 *     discount: Money,
 *     coupon_code: string|null,
 *     shipping_fee: Money,
 *     tax: Money,
 *     total: Money,
 *     refunded: Money,
 *     shipment: array{
 *         carrier: string|null,
 *         tracking_number: string|null,
 *         status: string|null,
 *     }|null,
 *     receipt_url: string,
 *     updated_at: string|null,
 * }
 *
 * @phpstan-type AdminShop array{
 *     id: int,
 *     name: string,
 *     storefront_url: string,
 *     is_live: bool,
 *     currency: string,
 *     default_language: string,
 *     languages: list<string>,
 *     timezone: string|null,
 *     prices_include_tax: bool,
 *     plan: 'freemium'|'basic'|'pro'|'business',
 *     headless: bool,
 * }
 *
 * @phpstan-type AppTokenRequest array{
 *     grant_type: 'authorization_code',
 *     client_id: string,
 *     code: string,
 *     redirect_uri: string,
 *     code_verifier: string,
 * }
 *
 * @phpstan-type AppTokenResponse array{
 *     api_key: string,
 *     scopes: list<AdminApiScope>,
 *     shop: array{
 *         id: int,
 *         name: string,
 *     },
 * }
 *
 * @phpstan-type ApiKey array{
 *     id: int,
 *     name: string,
 *     type: 'publishable'|'secret',
 *     key: string,
 *     allowed_origins: list<string>|null,
 *     oauth_client_id: string|null,
 *     redirect_uris: list<string>,
 *     created_by_app: string|null,
 *     last_used_at: string|null,
 *     created_at: string|null,
 *     updated_at: string|null,
 * }
 *
 * @phpstan-type ApiKeyInput array{
 *     name: string,
 *     type: 'publishable'|'secret',
 *     allowed_origins?: list<string>,
 *     redirect_uris?: list<string>|null,
 * }
 *
 * @phpstan-type LocalizedText array<string, string|null>|list<mixed>
 *
 * @phpstan-type AdminProduct array{
 *     id: int,
 *     slug: string,
 *     title: LocalizedText,
 *     short_description: LocalizedText,
 *     description: LocalizedText,
 *     sku: string|null,
 *     part_number: string|null,
 *     type: 'physical'|'digital',
 *     download_limit: int|null,
 *     document_url: string|null,
 *     status: bool,
 *     is_featured: bool|null,
 *     noindex: bool|null,
 *     price: float|int|null,
 *     sale_price: float|int|null,
 *     is_sale_scheduled: bool|null,
 *     sale_starts_at: string|null,
 *     sale_ends_at: string|null,
 *     wholesale_price: float|int|null,
 *     manage_stock: bool|null,
 *     stock_quantity: int|null,
 *     low_stock_amount: int|null,
 *     stock_status: 'in_stock'|'out_of_stock'|null,
 *     unit_id: int|null,
 *     tax_class_id: int|null,
 *     weight: float|int|null,
 *     length: float|int|null,
 *     width: float|int|null,
 *     height: float|int|null,
 *     taxonomy_term_ids: list<int>,
 *     spec_template_id: int|null,
 *     variable_product_id: int|null,
 *     thumbnail: string|null,
 *     gallery: list<string>,
 *     created_at: string|null,
 *     updated_at: string|null,
 * }
 *
 * @phpstan-type ProductInput array{
 *     title?: string,
 *     translations?: array<string, string|null>|null,
 *     short_description?: string|null,
 *     short_description_translations?: array<string, string|null>|null,
 *     description?: string|null,
 *     description_translations?: array<string, string|null>|null,
 *     taxonomy_term_ids?: list<int>|null,
 *     images?: list<array{
 *         url?: string|null,
 *         data?: string|null,
 *     }>|null,
 *     image_urls?: list<string>|null,
 *     sku?: string|null,
 *     part_number?: string|null,
 *     price?: float|int|null,
 *     sale_price?: float|int|null,
 *     is_sale_scheduled?: bool|null,
 *     sale_starts_at?: string|null,
 *     sale_ends_at?: string|null,
 *     wholesale_price?: float|int|null,
 *     manage_stock?: bool|null,
 *     stock_quantity?: int|null,
 *     stock_status?: 'in_stock'|'out_of_stock'|null,
 *     low_stock_amount?: int|null,
 *     unit_id?: int|null,
 *     tax_class_id?: int|null,
 *     type?: 'physical'|'digital',
 *     digital_url?: string|null,
 *     download_limit?: int|null,
 *     document_url?: string|null,
 *     status?: bool,
 *     is_featured?: bool|null,
 *     noindex?: bool|null,
 *     weight?: float|int|null,
 *     length?: float|int|null,
 *     width?: float|int|null,
 *     height?: float|int|null,
 *     spec_template_id?: int|null,
 *     specs?: array<string, mixed>|null,
 *     related_product_ids?: list<int>|null,
 *     wholesale_prices?: list<array{
 *         wholesale_membership_id: int,
 *         price?: float|int|null,
 *     }>|null,
 *     quantity_tiers?: list<array{
 *         min_quantity: int,
 *         price: float|int,
 *     }>|null,
 * }
 *
 * @phpstan-type InventoryItemInput array{
 *     product_id?: int|null,
 *     sku?: string|null,
 *     stock_quantity?: int|null,
 *     stock_status?: 'in_stock'|'out_of_stock'|null,
 * }
 *
 * @phpstan-type InventoryResult array{
 *     id: int,
 *     sku: string|null,
 *     manage_stock: bool,
 *     stock_quantity: int|null,
 *     stock_status: string|null,
 *     updated_at: string|null,
 * }
 *
 * @phpstan-type AdminCustomer array{
 *     id: int,
 *     name: string,
 *     email: string|null,
 *     email_verified: bool,
 *     phone: string|null,
 *     phone_verified: bool,
 *     wholesale: bool,
 *     wholesale_membership_id: int|null,
 *     created_at: string|null,
 *     updated_at: string|null,
 * }
 *
 * @phpstan-type AdminCoupon array{
 *     id: int,
 *     code: string,
 *     type: 'percentage'|'fixed',
 *     value: float|int,
 *     min_order_value: float|int|null,
 *     max_discount_amount: float|int|null,
 *     usage_limit: int|null,
 *     usage_limit_per_customer: int|null,
 *     used_count: int|null,
 *     auto_apply: bool,
 *     first_order_only: bool,
 *     combinable_with_offers: bool,
 *     starts_at: string|null,
 *     ends_at: string|null,
 *     status: bool,
 *     created_at: string|null,
 *     updated_at: string|null,
 * }
 *
 * @phpstan-type CouponInput array{
 *     code?: string,
 *     type?: 'percentage'|'fixed',
 *     value?: float|int,
 *     min_order_value?: float|int|null,
 *     max_discount_amount?: float|int|null,
 *     usage_limit?: int|null,
 *     usage_limit_per_customer?: int|null,
 *     auto_apply?: bool|null,
 *     first_order_only?: bool|null,
 *     combinable_with_offers?: bool|null,
 *     starts_at?: string|null,
 *     ends_at?: string|null,
 *     taxonomy_filters?: array<string, list<int>|null>|null,
 *     taxonomy_labels?: array<string, string|null>|null,
 *     status?: bool|null,
 * }
 *
 * @phpstan-type AdminOffer array{
 *     id: int,
 *     name: string,
 *     type: 'buy_x_get_y'|'quantity_discount'|'amount_discount',
 *     config: OfferConfig,
 *     status: bool,
 *     combinable_with_coupons: bool,
 *     priority: int|null,
 *     usage_limit: int|null,
 *     used_count: int|null,
 *     starts_at: string|null,
 *     ends_at: string|null,
 *     created_at: string|null,
 *     updated_at: string|null,
 * }
 *
 * @phpstan-type OfferInput array{
 *     name?: string,
 *     type?: 'buy_x_get_y'|'quantity_discount'|'amount_discount',
 *     config?: OfferConfig,
 *     combinable_with_coupons?: bool|null,
 *     priority?: int|null,
 *     usage_limit?: int|null,
 *     starts_at?: string|null,
 *     ends_at?: string|null,
 *     status?: bool|null,
 * }
 *
 * @phpstan-type OfferConfig BuyXGetYConfig|TieredOfferConfig
 *
 * @phpstan-type OfferScope array{
 *     taxonomy_filters?: array<string, list<int>|null>|null,
 *     taxonomy_labels?: array<string, string|null>|null,
 *     product_ids?: list<int>|null,
 *     product_labels?: array<string, string|null>|null,
 *     exclude_product_ids?: list<int>|null,
 *     exclude_product_labels?: array<string, string|null>|null,
 * }|null
 *
 * @phpstan-type BuyXGetYConfig array{
 *     buy: array{
 *         qty: int,
 *         scope?: OfferScope,
 *     },
 *     reward: array{
 *         kind: 'items'|'discount',
 *         qty?: int|null,
 *         get_scope?: OfferScope,
 *         value_type: 'percentage'|'fixed',
 *         value: float|int,
 *         applies_to?: 'buy_items'|'order'|null,
 *     },
 *     max_applications?: int|null,
 * }
 *
 * @phpstan-type TieredOfferConfig array{
 *     tiers: list<array{
 *         min_qty?: int,
 *         min_subtotal?: float|int,
 *         value_type: 'percentage'|'fixed',
 *         value: float|int,
 *     }>,
 *     max_discount_amount?: float|int|null,
 *     scope?: OfferScope,
 * }
 *
 * @phpstan-type AdminQuotation array{
 *     id: int,
 *     quotation_number: string,
 *     status: QuotationStatus,
 *     enquiry_id: int|null,
 *     order_id: int|null,
 *     previous_quotation_id: int|null,
 *     customer_id: int|null,
 *     customer_name: string|null,
 *     customer_email: string|null,
 *     customer_phone: string|null,
 *     shipping_country_code: string|null,
 *     shipping_city: string|null,
 *     shipping_state: string|null,
 *     shipping_address_line_1: string|null,
 *     shipping_address_line_2: string|null,
 *     shipping_postal_code: string|null,
 *     billing_different: bool,
 *     billing_name: string|null,
 *     billing_phone: string|null,
 *     billing_country_code: string|null,
 *     billing_city: string|null,
 *     billing_state: string|null,
 *     billing_address_line_1: string|null,
 *     billing_address_line_2: string|null,
 *     billing_postal_code: string|null,
 *     notes: string|null,
 *     currency: string|null,
 *     items: list<array{
 *         product_id: int|null,
 *         title: string|null,
 *         sku: string|null,
 *         quantity: int,
 *         price: float|int,
 *         total: float|int,
 *         note: string|null,
 *     }>,
 *     subtotal: float|int,
 *     shipping_fee: float|int,
 *     tax: float|int,
 *     total: float|int,
 *     allowed_actions: list<'update'|'send'|'publish'|'revise'|'agree'|'agree_and_order'|'create_order'|'cancel'>,
 *     sent_at: string|null,
 *     agreed_at: string|null,
 *     cancelled_at: string|null,
 *     created_at: string|null,
 *     updated_at: string|null,
 * }
 *
 * @phpstan-type QuotationInput array{
 *     customer_id?: int|null,
 *     customer_name?: string|null,
 *     customer_email?: string|null,
 *     customer_phone?: string|null,
 *     items?: list<array{
 *         product_id: int,
 *         quantity: int,
 *         price: float|int,
 *         note?: string|null,
 *     }>,
 *     shipping_fee?: float|int,
 *     shipping_country_code?: string|null,
 *     shipping_city?: string|null,
 *     shipping_state?: string|null,
 *     shipping_address_line_1?: string|null,
 *     shipping_address_line_2?: string|null,
 *     shipping_postal_code?: string|null,
 *     billing_different?: bool|null,
 *     billing_name?: string|null,
 *     billing_phone?: string|null,
 *     billing_country_code?: string|null,
 *     billing_city?: string|null,
 *     billing_state?: string|null,
 *     billing_address_line_1?: string|null,
 *     billing_address_line_2?: string|null,
 *     billing_postal_code?: string|null,
 *     notes?: string|null,
 *     intro?: string|null,
 *     pdf_template_id?: int|null,
 * }
 *
 * @phpstan-type AdminReview array{
 *     id: int,
 *     product_id: int|null,
 *     customer_id: int|null,
 *     order_id: int|null,
 *     rating: int,
 *     title: string|null,
 *     body: string|null,
 *     author_name: string|null,
 *     verified_buyer: bool,
 *     locale: string|null,
 *     status: ReviewStatus,
 *     reply: array{
 *         body: string,
 *         updated_at: string|null,
 *     }|null,
 *     published_at: string|null,
 *     created_at: string|null,
 *     updated_at: string|null,
 * }
 *
 * @phpstan-type WebhookUrl string
 *
 * @phpstan-type WebhookEndpoint array{
 *     id: int,
 *     url: string,
 *     events: list<SubscribableWebhookEvent>,
 *     active: bool,
 *     disabled_at: string|null,
 *     created_at: string|null,
 *     updated_at: string|null,
 * }
 *
 * @phpstan-type Enquiry array{
 *     id: int,
 *     enquiry_number: string,
 *     status: EnquiryStatus,
 *     payment_status: PaymentStatus,
 *     revision: int|null,
 *     customer_id: int|null,
 *     wholesale_membership_id: int|null,
 *     order_id: int|null,
 *     customer_name: string|null,
 *     customer_email: string|null,
 *     customer_phone: string|null,
 *     shipping_country_code: string|null,
 *     shipping_city: string|null,
 *     shipping_state: string|null,
 *     shipping_address_line_1: string|null,
 *     shipping_address_line_2: string|null,
 *     shipping_postal_code: string|null,
 *     notes: string|null,
 *     currency: string|null,
 *     items: list<array{
 *         product_id: int|null,
 *         title: string|null,
 *         sku: string|null,
 *         requested_quantity: int|null,
 *         requested_price: float|int,
 *         quoted_quantity: int|null,
 *         quoted_price: float|int|null,
 *         total: float|int,
 *         note: string|null,
 *     }>,
 *     subtotal: float|int,
 *     shipping_fee: float|int,
 *     tax: float|int,
 *     total: float|int,
 *     quoted_at: string|null,
 *     agreed_at: string|null,
 *     completed_at: string|null,
 *     cancelled_at: string|null,
 *     created_at: string|null,
 *     updated_at: string|null,
 * }
 *
 * @phpstan-type WebhookEnvelope array{
 *     id: string,
 *     type: WebhookEvent,
 *     created_at: string,
 *     data: array<string, mixed>,
 * }
 *
 * @phpstan-type ProductWebhookPayload array{
 *     id: string,
 *     type: 'product.created'|'product.updated'|'product.deleted'|'product.stock_changed',
 *     created_at: string,
 *     data: array{
 *         product: array{
 *             id: int,
 *             slug: string,
 *             changes: list<'price'|'stock'|'content'>,
 *         },
 *     },
 * }
 *
 * @phpstan-type CustomerWebhookPayload array{
 *     id: string,
 *     type: 'customer.created'|'customer.updated',
 *     created_at: string,
 *     data: array{
 *         customer: AdminCustomer,
 *     },
 * }
 *
 * @phpstan-type ProductWriteResult array{
 *     data: AdminProduct,
 *     warnings: list<string>,
 * }
 *
 * @phpstan-type QuotationActionResult array{
 *     data: AdminQuotation,
 *     order: array{
 *         id: int,
 *         order_number: string,
 *     }|null,
 * }
 *
 * @phpstan-type CursorPageLinks array{
 *     first: null,
 *     last: null,
 *     prev: string|null,
 *     next: string|null,
 * }
 *
 * @phpstan-type CursorPageMeta array{
 *     path: string,
 *     per_page: int,
 *     next_cursor: string|null,
 *     prev_cursor: string|null,
 * }
 *
 * @phpstan-type NumberedPageLinks array{
 *     first: string,
 *     last: string,
 *     prev: string|null,
 *     next: string|null,
 * }
 *
 * @phpstan-type NumberedPageMeta array{
 *     current_page: int,
 *     from: int|null,
 *     last_page: int,
 *     links: list<array{
 *         url: string|null,
 *         label: string,
 *         page?: int|null,
 *         active: bool,
 *     }>,
 *     path: string,
 *     per_page: int,
 *     to: int|null,
 *     total: int,
 * }
 *
 * @phpstan-type CartItemInput array{
 *     product_id: int,
 *     quantity?: int|null,
 * }
 *
 * @phpstan-type CheckoutOptions array{
 *     return_path?: string|null,
 *     cart_path?: string|null,
 *     attribution?: array{
 *         utm_source?: string|null,
 *         utm_medium?: string|null,
 *         utm_campaign?: string|null,
 *         fbclid?: string|null,
 *         gclid?: string|null,
 *         gbraid?: string|null,
 *         wbraid?: string|null,
 *         dclid?: string|null,
 *         msclkid?: string|null,
 *         ttclid?: string|null,
 *         li_fat_id?: string|null,
 *         twclid?: string|null,
 *     }|null,
 * }
 *
 * @phpstan-type CreatedApiKey array{
 *     data: ApiKey,
 *     value: string,
 * }
 *
 * @phpstan-type OrderStatusInput array{
 *     status: 'pending'|'processing'|'ready_to_ship'|'shipped'|'completed'|'cancelled',
 *     note?: string|null,
 *     notify_customer?: bool|null,
 *     updated_at?: UpdatedAtGuard,
 * }
 *
 * @phpstan-type OrderShipmentInput array{
 *     carrier: 'dhl'|'ups'|'fedex'|'aramex'|'smsa'|'dhl_ecommerce'|'babel_express'|'other',
 *     carrier_name?: string|null,
 *     tracking_number: string,
 *     tracking_url?: string|null,
 * }
 *
 * @phpstan-type AdminCustomerInput array{
 *     name: string,
 *     email: string,
 *     phone?: string|null,
 * }
 *
 * @phpstan-type QuotationSendInput array{
 *     recipient?: string|null,
 *     cc?: list<string>|null,
 *     subject?: string|null,
 *     message?: string|null,
 * }
 *
 * @phpstan-type WebhookEndpointInput array{
 *     url: WebhookUrl,
 *     events: list<SubscribableWebhookEvent>,
 *     active?: bool,
 * }
 *
 * @phpstan-type CreatedWebhookEndpoint array{
 *     data: WebhookEndpoint,
 *     secret: string,
 * }
 */
final class Schemas
{
    private function __construct() {}
}
