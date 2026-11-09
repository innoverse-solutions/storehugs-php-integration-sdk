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
 *         perPage: int,
 *         nextCursor: string|null,
 *         prevCursor: string|null,
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
 *         currentPage: int,
 *         from: int|null,
 *         lastPage: int,
 *         links: list<array{
 *             url: string|null,
 *             label: string,
 *             page?: int|null,
 *             active: bool,
 *         }>,
 *         path: string,
 *         perPage: int,
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
 *     defaultLanguage: string,
 *     languages: list<string>,
 *     currency: string,
 *     pricesIncludeTax: bool,
 *     paymentMethods: list<array{
 *         id: string,
 *         label: string,
 *     }>,
 *     cartAvailable: bool,
 *     shippingEstimates: bool,
 *     siteUrl: string|null,
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
 *     regularPrice: Money,
 *     onSale: bool,
 *     inStock: bool,
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
 *     regularPrice: Money,
 *     onSale: bool,
 *     inStock: bool,
 *     rating: Rating,
 *     image: string|null,
 *     description: string|null,
 *     shortDescription: string|null,
 *     partNumber: string|null,
 *     unit: string|null,
 *     stockQuantity: int|null,
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
 *         averageRating: float|int,
 *         totalReviews: int,
 *         distribution: array{
 *             '5': int,
 *             '4': int,
 *             '3': int,
 *             '2': int,
 *             '1': int,
 *         },
 *     }|null,
 *     variants: list<array{
 *         productId: int,
 *         slug: string,
 *         isThisProduct: bool,
 *         options: list<array{
 *             type: string|null,
 *             value: string|null,
 *         }>,
 *         price: Money,
 *         inStock: bool,
 *     }>,
 * }
 *
 * @phpstan-type Taxonomy array{
 *     id: int,
 *     slug: string,
 *     name: string|null,
 *     isTree: bool,
 *     isMultiple: bool,
 * }
 *
 * @phpstan-type TaxonomyTerm array{
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
 *     cartToken: string,
 *     mode: 'checkout'|'enquiry',
 *     customerId: int|null,
 *     items: list<CartItem>,
 *     totalQuantity: int,
 *     subtotal: Money,
 *     discount: Money,
 *     coupon: array{
 *         code: string,
 *         autoApplied: bool,
 *     }|null,
 *     couponError: string|null,
 *     offers: list<string>,
 *     taxAdded: Money,
 *     taxIncluded: Money,
 *     totalBeforeShipping: Money,
 * }
 *
 * @phpstan-type CartItem array{
 *     id: int,
 *     productId: int,
 *     slug: string|null,
 *     title: string|null,
 *     image: string|null,
 *     quantity: int,
 *     unitPrice: Money,
 *     lineTotal: Money,
 * }
 *
 * @phpstan-type ShippingDestination array{
 *     shippingCountryCode: string,
 *     shippingCity?: string|null,
 *     shippingState?: string|null,
 *     shippingPostalCode?: string|null,
 *     shippingAddressLine1?: string|null,
 *     shippingStateId?: int|null,
 *     shippingCityId?: int|null,
 *     shippingNeighbourhoodId?: int|null,
 *     shippingLocationUnlisted?: bool|null,
 * }
 *
 * @phpstan-type ShippingEstimate array{
 *     shipsTo: bool,
 *     shipsFree: bool,
 *     quotes: list<array{
 *         methodId: int,
 *         name: string,
 *         cost: float|int,
 *         isLiveRate: bool,
 *         isCarrierPrice: bool,
 *     }>,
 * }
 *
 * @phpstan-type CheckoutLink array{
 *     checkoutUrl: string,
 *     expiresAt: string,
 *     mode: 'checkout'|'enquiry',
 *     requiresSignIn: bool,
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
 *     emailVerified: bool,
 *     phone: string|null,
 *     wholesale: array{
 *         name: string,
 *     }|null,
 *     accountUrl: string,
 * }
 *
 * @phpstan-type CustomerAddress array{
 *     id: int,
 *     label: string|null,
 *     name: string,
 *     phone: string,
 *     countryCode: string,
 *     state: string|null,
 *     stateId: int|null,
 *     city: string|null,
 *     cityId: int|null,
 *     neighbourhood: string|null,
 *     neighbourhoodId: int|null,
 *     addressLine1: string,
 *     addressLine2: string|null,
 *     postalCode: string|null,
 *     isDefault: bool,
 * }
 *
 * @phpstan-type CustomerAddressInput array{
 *     label?: string|null,
 *     name: string,
 *     phone: string,
 *     countryCode: string,
 *     city: string,
 *     state?: string|null,
 *     addressLine1: string,
 *     addressLine2?: string|null,
 *     postalCode?: string|null,
 *     isDefault?: bool,
 *     shippingStateId?: int|null,
 *     shippingCityId?: int|null,
 *     shippingNeighbourhoodId?: int|null,
 *     shippingLocationUnlisted?: bool|null,
 * }
 *
 * @phpstan-type Order array{
 *     id: int,
 *     orderNumber: string,
 *     status: OrderStatus,
 *     paymentStatus: PaymentStatus|null,
 *     paymentMethod: string|null,
 *     source: string,
 *     placedAt: string|null,
 *     paidAt: string|null,
 *     customer: array{
 *         id: int|null,
 *         name: string|null,
 *         email: string|null,
 *         phone: string|null,
 *     },
 *     shippingAddress: array{
 *         line1: string|null,
 *         line2: string|null,
 *         neighbourhood: string|null,
 *         city: string|null,
 *         state: string|null,
 *         postalCode: string|null,
 *         countryCode: string|null,
 *     },
 *     billingAddress: array{
 *         name: string|null,
 *         phone: string|null,
 *         line1: string|null,
 *         line2: string|null,
 *         city: string|null,
 *         state: string|null,
 *         postalCode: string|null,
 *         countryCode: string|null,
 *     }|null,
 *     shippingMethod: string|null,
 *     notes: string|null,
 *     items: list<array{
 *         productId: int|null,
 *         title: string|null,
 *         sku: string|null,
 *         quantity: int,
 *         unitPrice: Money,
 *         total: Money,
 *     }>,
 *     subtotal: Money,
 *     discount: Money,
 *     couponCode: string|null,
 *     shippingFee: Money,
 *     tax: Money,
 *     total: Money,
 *     refunded: Money,
 *     shipment: array{
 *         carrier: string|null,
 *         trackingNumber: string|null,
 *         status: string|null,
 *     }|null,
 *     receiptUrl: string,
 *     updatedAt: string|null,
 * }
 *
 * @phpstan-type AdminShop array{
 *     id: int,
 *     name: string,
 *     storefrontUrl: string,
 *     isLive: bool,
 *     currency: string,
 *     defaultLanguage: string,
 *     languages: list<string>,
 *     timezone: string|null,
 *     pricesIncludeTax: bool,
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
 *     allowedOrigins: list<string>|null,
 *     oauthClientId: string|null,
 *     redirectUris: list<string>,
 *     createdByApp: string|null,
 *     lastUsedAt: string|null,
 *     createdAt: string|null,
 *     updatedAt: string|null,
 * }
 *
 * @phpstan-type ApiKeyInput array{
 *     name: string,
 *     type: 'publishable'|'secret',
 *     allowedOrigins?: list<string>,
 *     redirectUris?: list<string>|null,
 * }
 *
 * @phpstan-type LocalizedText array<string, string|null>|list<mixed>
 *
 * @phpstan-type AdminProduct array{
 *     id: int,
 *     slug: string,
 *     title: LocalizedText,
 *     shortDescription: LocalizedText,
 *     description: LocalizedText,
 *     sku: string|null,
 *     partNumber: string|null,
 *     type: 'physical'|'digital',
 *     downloadLimit: int|null,
 *     documentUrl: string|null,
 *     status: bool,
 *     isFeatured: bool|null,
 *     noindex: bool|null,
 *     price: float|int|null,
 *     salePrice: float|int|null,
 *     isSaleScheduled: bool|null,
 *     saleStartsAt: string|null,
 *     saleEndsAt: string|null,
 *     wholesalePrice: float|int|null,
 *     manageStock: bool|null,
 *     stockQuantity: int|null,
 *     lowStockAmount: int|null,
 *     stockStatus: 'in_stock'|'out_of_stock'|null,
 *     unitId: int|null,
 *     taxClassId: int|null,
 *     weight: float|int|null,
 *     length: float|int|null,
 *     width: float|int|null,
 *     height: float|int|null,
 *     taxonomyTermIds: list<int>,
 *     relatedProductIds: list<int>,
 *     recommendedProductIds: list<int>,
 *     specTemplateId: int|null,
 *     variableProductId: int|null,
 *     thumbnail: string|null,
 *     gallery: list<string>,
 *     createdAt: string|null,
 *     updatedAt: string|null,
 * }
 *
 * @phpstan-type ProductInput array{
 *     title?: string,
 *     translations?: array<string, string|null>|null,
 *     shortDescription?: string|null,
 *     shortDescriptionTranslations?: array<string, string|null>|null,
 *     description?: string|null,
 *     descriptionTranslations?: array<string, string|null>|null,
 *     taxonomyTermIds?: list<int>|null,
 *     images?: list<array{
 *         url?: string|null,
 *         data?: string|null,
 *     }>|null,
 *     imageUrls?: list<string>|null,
 *     sku?: string|null,
 *     partNumber?: string|null,
 *     price?: float|int|null,
 *     salePrice?: float|int|null,
 *     isSaleScheduled?: bool|null,
 *     saleStartsAt?: string|null,
 *     saleEndsAt?: string|null,
 *     wholesalePrice?: float|int|null,
 *     manageStock?: bool|null,
 *     stockQuantity?: int|null,
 *     stockStatus?: 'in_stock'|'out_of_stock'|null,
 *     lowStockAmount?: int|null,
 *     unitId?: int|null,
 *     taxClassId?: int|null,
 *     type?: 'physical'|'digital',
 *     digitalUrl?: string|null,
 *     downloadLimit?: int|null,
 *     documentUrl?: string|null,
 *     status?: bool,
 *     isFeatured?: bool|null,
 *     noindex?: bool|null,
 *     weight?: float|int|null,
 *     length?: float|int|null,
 *     width?: float|int|null,
 *     height?: float|int|null,
 *     specTemplateId?: int|null,
 *     specs?: array<string, mixed>|null,
 *     relatedProductIds?: list<int>|null,
 *     recommendedProductIds?: list<int>|null,
 *     wholesalePrices?: list<array{
 *         wholesaleMembershipId: int,
 *         price?: float|int|null,
 *     }>|null,
 *     quantityTiers?: list<array{
 *         minQuantity: int,
 *         price: float|int,
 *     }>|null,
 * }
 *
 * @phpstan-type InventoryItemInput array{
 *     productId?: int|null,
 *     sku?: string|null,
 *     stockQuantity?: int|null,
 *     stockStatus?: 'in_stock'|'out_of_stock'|null,
 * }
 *
 * @phpstan-type InventoryResult array{
 *     id: int,
 *     sku: string|null,
 *     manageStock: bool,
 *     stockQuantity: int|null,
 *     stockStatus: string|null,
 *     updatedAt: string|null,
 * }
 *
 * @phpstan-type AdminCustomer array{
 *     id: int,
 *     name: string,
 *     email: string|null,
 *     emailVerified: bool,
 *     phone: string|null,
 *     phoneVerified: bool,
 *     wholesale: bool,
 *     wholesaleMembershipId: int|null,
 *     createdAt: string|null,
 *     updatedAt: string|null,
 * }
 *
 * @phpstan-type AdminTaxonomy array{
 *     id: int,
 *     name: LocalizedText,
 *     slug: string,
 *     multipleTermsPerProduct: bool,
 *     nested: bool,
 *     terms: int,
 * }
 *
 * @phpstan-type AdminTaxonomyTerm array{
 *     id: int,
 *     taxonomyId: int,
 *     title: LocalizedText,
 *     slug: string,
 *     description: LocalizedText,
 *     parentId: int|null,
 *     sortOrder: int,
 *     image: string|null,
 *     createdAt: string|null,
 *     updatedAt: string|null,
 * }
 *
 * @phpstan-type TaxonomyTermInput array{
 *     title?: string,
 *     translations?: array<string, string|null>|null,
 *     description?: string|null,
 *     descriptionTranslations?: array<string, string|null>|null,
 *     parentId?: int|null,
 *     image?: array{
 *         url?: string|null,
 *         data?: string|null,
 *     }|null,
 * }
 *
 * @phpstan-type AdminVariableProduct array{
 *     id: int,
 *     name: string,
 *     options: list<array{
 *         id: int,
 *         name: LocalizedText,
 *     }>,
 *     variants: list<array{
 *         productId: int,
 *         title: LocalizedText,
 *         sku: string|null,
 *         status: bool,
 *         options: list<array{
 *             optionId: int,
 *             valueId: int,
 *             value: LocalizedText,
 *         }>,
 *     }>,
 *     createdAt: string|null,
 *     updatedAt: string|null,
 * }
 *
 * @phpstan-type AdminVariantOption array{
 *     id: int,
 *     name: LocalizedText,
 *     displayMode: 'text'|'color'|'image',
 *     values: list<array{
 *         id: int,
 *         name: LocalizedText,
 *         color: string|null,
 *     }>,
 * }
 *
 * @phpstan-type VariantOptionInput array{
 *     name?: string,
 *     translations?: array<string, string|null>|null,
 *     displayMode?: 'text'|'color'|'image',
 * }
 *
 * @phpstan-type VariantValueInput array{
 *     name?: string,
 *     translations?: array<string, string|null>|null,
 *     color?: string|null,
 * }
 *
 * @phpstan-type VariableProductInput array{
 *     title?: string,
 *     translations?: array<string, string|null>|null,
 *     shortDescription?: string|null,
 *     shortDescriptionTranslations?: array<string, string|null>|null,
 *     description?: string|null,
 *     descriptionTranslations?: array<string, string|null>|null,
 *     taxonomyTermIds?: list<int>|null,
 *     images?: list<array{
 *         url?: string|null,
 *         data?: string|null,
 *     }>|null,
 *     imageUrls?: list<string>|null,
 *     sku?: string|null,
 *     partNumber?: string|null,
 *     price?: float|int|null,
 *     salePrice?: float|int|null,
 *     isSaleScheduled?: bool|null,
 *     saleStartsAt?: string|null,
 *     saleEndsAt?: string|null,
 *     wholesalePrice?: float|int|null,
 *     manageStock?: bool|null,
 *     stockQuantity?: int|null,
 *     stockStatus?: 'in_stock'|'out_of_stock'|null,
 *     lowStockAmount?: int|null,
 *     unitId?: int|null,
 *     taxClassId?: int|null,
 *     type?: 'physical'|'digital',
 *     digitalUrl?: string|null,
 *     downloadLimit?: int|null,
 *     documentUrl?: string|null,
 *     status?: bool,
 *     isFeatured?: bool|null,
 *     noindex?: bool|null,
 *     weight?: float|int|null,
 *     length?: float|int|null,
 *     width?: float|int|null,
 *     height?: float|int|null,
 *     specTemplateId?: int|null,
 *     specs?: array<string, mixed>|null,
 *     relatedProductIds?: list<int>|null,
 *     recommendedProductIds?: list<int>|null,
 *     wholesalePrices?: list<array{
 *         wholesaleMembershipId: int,
 *         price?: float|int|null,
 *     }>|null,
 *     quantityTiers?: list<array{
 *         minQuantity: int,
 *         price: float|int,
 *     }>|null,
 *     optionTranslations?: list<array{
 *         name: string,
 *         translations: array<string, string|null>,
 *     }>|null,
 *     variants?: list<VariantInput>,
 * }
 *
 * @phpstan-type VariantInput array{
 *     options: list<array{
 *         name: string,
 *         value: string,
 *     }>,
 *     sku?: string|null,
 *     partNumber?: string|null,
 *     price?: float|int|null,
 *     salePrice?: float|int|null,
 *     manageStock?: bool|null,
 *     stockQuantity?: int|null,
 *     stockStatus?: 'in_stock'|'out_of_stock'|null,
 *     lowStockAmount?: int|null,
 *     status?: bool,
 *     images?: list<array{
 *         url?: string|null,
 *         data?: string|null,
 *     }>|null,
 *     specs?: array<string, mixed>|null,
 *     taxonomyTermIds?: list<int>|null,
 * }
 *
 * @phpstan-type AdminSpecTemplate array{
 *     id: int,
 *     name: LocalizedText,
 *     description: LocalizedText,
 *     source: 'mine'|'public',
 *     category: string|null,
 *     properties: int,
 *     createdAt: string|null,
 *     updatedAt: string|null,
 * }
 *
 * @phpstan-type AdminSpecTemplateDetail array{
 *     id: int,
 *     name: LocalizedText,
 *     description: LocalizedText,
 *     source: 'mine'|'public',
 *     category: string|null,
 *     properties: int,
 *     createdAt: string|null,
 *     updatedAt: string|null,
 *     groups: list<array{
 *         id: int,
 *         name: LocalizedText,
 *         properties: list<AdminSpecProperty>,
 *     }>,
 *     ungroupedProperties: list<AdminSpecProperty>,
 * }
 *
 * @phpstan-type AdminSpecProperty array{
 *     id: int,
 *     name: LocalizedText,
 *     type: 'text'|'number'|'boolean'|'select',
 *     unit: LocalizedText|null,
 *     required: bool,
 *     options: list<array{
 *         key: string,
 *         label: LocalizedText,
 *     }>|null,
 * }
 *
 * @phpstan-type AdminUnit array{
 *     id: int,
 *     name: LocalizedText,
 *     products: int,
 *     createdAt: string|null,
 *     updatedAt: string|null,
 * }
 *
 * @phpstan-type UnitInput array{
 *     name?: string,
 *     translations?: array<string, string|null>|null,
 * }
 *
 * @phpstan-type AdminCoupon array{
 *     id: int,
 *     code: string,
 *     type: 'percentage'|'fixed',
 *     value: float|int,
 *     minOrderValue: float|int|null,
 *     maxDiscountAmount: float|int|null,
 *     usageLimit: int|null,
 *     usageLimitPerCustomer: int|null,
 *     usedCount: int|null,
 *     autoApply: bool,
 *     firstOrderOnly: bool,
 *     combinableWithOffers: bool,
 *     startsAt: string|null,
 *     endsAt: string|null,
 *     status: bool,
 *     createdAt: string|null,
 *     updatedAt: string|null,
 * }
 *
 * @phpstan-type CouponInput array{
 *     code?: string,
 *     type?: 'percentage'|'fixed',
 *     value?: float|int,
 *     minOrderValue?: float|int|null,
 *     maxDiscountAmount?: float|int|null,
 *     usageLimit?: int|null,
 *     usageLimitPerCustomer?: int|null,
 *     autoApply?: bool|null,
 *     firstOrderOnly?: bool|null,
 *     combinableWithOffers?: bool|null,
 *     startsAt?: string|null,
 *     endsAt?: string|null,
 *     taxonomyFilters?: array<string, list<int>|null>|null,
 *     taxonomyLabels?: array<string, string|null>|null,
 *     status?: bool|null,
 * }
 *
 * @phpstan-type AdminOffer array{
 *     id: int,
 *     name: string,
 *     type: 'buy_x_get_y'|'quantity_discount'|'amount_discount',
 *     config: OfferConfig,
 *     status: bool,
 *     combinableWithCoupons: bool,
 *     priority: int|null,
 *     usageLimit: int|null,
 *     usedCount: int|null,
 *     startsAt: string|null,
 *     endsAt: string|null,
 *     createdAt: string|null,
 *     updatedAt: string|null,
 * }
 *
 * @phpstan-type OfferInput array{
 *     name?: string,
 *     type?: 'buy_x_get_y'|'quantity_discount'|'amount_discount',
 *     config?: OfferConfig,
 *     combinableWithCoupons?: bool|null,
 *     priority?: int|null,
 *     usageLimit?: int|null,
 *     startsAt?: string|null,
 *     endsAt?: string|null,
 *     status?: bool|null,
 * }
 *
 * @phpstan-type OfferConfig BuyXGetYConfig|TieredOfferConfig
 *
 * @phpstan-type OfferScope array{
 *     taxonomyFilters?: array<string, list<int>|null>|null,
 *     taxonomyLabels?: array<string, string|null>|null,
 *     productIds?: list<int>|null,
 *     productLabels?: array<string, string|null>|null,
 *     excludeProductIds?: list<int>|null,
 *     excludeProductLabels?: array<string, string|null>|null,
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
 *         getScope?: OfferScope,
 *         valueType: 'percentage'|'fixed',
 *         value: float|int,
 *         appliesTo?: 'buy_items'|'order'|null,
 *     },
 *     maxApplications?: int|null,
 * }
 *
 * @phpstan-type TieredOfferConfig array{
 *     tiers: list<array{
 *         minQty?: int,
 *         minSubtotal?: float|int,
 *         valueType: 'percentage'|'fixed',
 *         value: float|int,
 *     }>,
 *     maxDiscountAmount?: float|int|null,
 *     scope?: OfferScope,
 * }
 *
 * @phpstan-type AdminQuotation array{
 *     id: int,
 *     quotationNumber: string,
 *     status: QuotationStatus,
 *     enquiryId: int|null,
 *     orderId: int|null,
 *     previousQuotationId: int|null,
 *     customerId: int|null,
 *     customerName: string|null,
 *     customerEmail: string|null,
 *     customerPhone: string|null,
 *     shippingCountryCode: string|null,
 *     shippingCity: string|null,
 *     shippingState: string|null,
 *     shippingAddressLine1: string|null,
 *     shippingAddressLine2: string|null,
 *     shippingPostalCode: string|null,
 *     billingDifferent: bool,
 *     billingName: string|null,
 *     billingPhone: string|null,
 *     billingCountryCode: string|null,
 *     billingCity: string|null,
 *     billingState: string|null,
 *     billingAddressLine1: string|null,
 *     billingAddressLine2: string|null,
 *     billingPostalCode: string|null,
 *     notes: string|null,
 *     currency: string|null,
 *     items: list<array{
 *         productId: int|null,
 *         title: string|null,
 *         sku: string|null,
 *         quantity: int,
 *         price: float|int,
 *         total: float|int,
 *         note: string|null,
 *     }>,
 *     subtotal: float|int,
 *     shippingFee: float|int,
 *     tax: float|int,
 *     total: float|int,
 *     allowedActions: list<'update'|'send'|'publish'|'revise'|'agree'|'agree_and_order'|'create_order'|'cancel'>,
 *     sentAt: string|null,
 *     agreedAt: string|null,
 *     cancelledAt: string|null,
 *     createdAt: string|null,
 *     updatedAt: string|null,
 * }
 *
 * @phpstan-type QuotationInput array{
 *     customerId?: int|null,
 *     customerName?: string|null,
 *     customerEmail?: string|null,
 *     customerPhone?: string|null,
 *     items?: list<array{
 *         productId: int,
 *         quantity: int,
 *         price: float|int,
 *         note?: string|null,
 *     }>,
 *     shippingFee?: float|int,
 *     shippingCountryCode?: string|null,
 *     shippingCity?: string|null,
 *     shippingState?: string|null,
 *     shippingAddressLine1?: string|null,
 *     shippingAddressLine2?: string|null,
 *     shippingPostalCode?: string|null,
 *     billingDifferent?: bool|null,
 *     billingName?: string|null,
 *     billingPhone?: string|null,
 *     billingCountryCode?: string|null,
 *     billingCity?: string|null,
 *     billingState?: string|null,
 *     billingAddressLine1?: string|null,
 *     billingAddressLine2?: string|null,
 *     billingPostalCode?: string|null,
 *     notes?: string|null,
 *     intro?: string|null,
 *     pdfTemplateId?: int|null,
 * }
 *
 * @phpstan-type AdminReview array{
 *     id: int,
 *     productId: int|null,
 *     customerId: int|null,
 *     orderId: int|null,
 *     rating: int,
 *     title: string|null,
 *     body: string|null,
 *     authorName: string|null,
 *     verifiedBuyer: bool,
 *     locale: string|null,
 *     status: ReviewStatus,
 *     reply: array{
 *         body: string,
 *         updatedAt: string|null,
 *     }|null,
 *     publishedAt: string|null,
 *     createdAt: string|null,
 *     updatedAt: string|null,
 * }
 *
 * @phpstan-type WebhookUrl string
 *
 * @phpstan-type WebhookEndpoint array{
 *     id: int,
 *     url: string,
 *     events: list<SubscribableWebhookEvent>,
 *     active: bool,
 *     disabledAt: string|null,
 *     createdAt: string|null,
 *     updatedAt: string|null,
 * }
 *
 * @phpstan-type Enquiry array{
 *     id: int,
 *     enquiryNumber: string,
 *     status: EnquiryStatus,
 *     paymentStatus: PaymentStatus,
 *     revision: int|null,
 *     customerId: int|null,
 *     wholesaleMembershipId: int|null,
 *     orderId: int|null,
 *     customerName: string|null,
 *     customerEmail: string|null,
 *     customerPhone: string|null,
 *     shippingCountryCode: string|null,
 *     shippingCity: string|null,
 *     shippingState: string|null,
 *     shippingAddressLine1: string|null,
 *     shippingAddressLine2: string|null,
 *     shippingPostalCode: string|null,
 *     notes: string|null,
 *     currency: string|null,
 *     items: list<array{
 *         productId: int|null,
 *         title: string|null,
 *         sku: string|null,
 *         requestedQuantity: int|null,
 *         requestedPrice: float|int,
 *         quotedQuantity: int|null,
 *         quotedPrice: float|int|null,
 *         total: float|int,
 *         note: string|null,
 *     }>,
 *     subtotal: float|int,
 *     shippingFee: float|int,
 *     tax: float|int,
 *     total: float|int,
 *     quotedAt: string|null,
 *     agreedAt: string|null,
 *     completedAt: string|null,
 *     cancelledAt: string|null,
 *     createdAt: string|null,
 *     updatedAt: string|null,
 * }
 *
 * @phpstan-type WebhookEnvelope array{
 *     id: string,
 *     type: WebhookEvent,
 *     createdAt: string,
 *     data: array<string, mixed>,
 * }
 *
 * @phpstan-type ProductWebhookPayload array{
 *     id: string,
 *     type: 'product.created'|'product.updated'|'product.deleted'|'product.stock_changed',
 *     createdAt: string,
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
 *     createdAt: string,
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
 *         orderNumber: string,
 *     }|null,
 * }
 *
 * @phpstan-type TaxonomyTermWriteResult array{
 *     data: AdminTaxonomyTerm,
 *     warnings: list<string>,
 * }
 *
 * @phpstan-type VariableProductWriteResult array{
 *     data: AdminVariableProduct,
 *     warnings: list<string>,
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
 *     perPage: int,
 *     nextCursor: string|null,
 *     prevCursor: string|null,
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
 *     currentPage: int,
 *     from: int|null,
 *     lastPage: int,
 *     links: list<array{
 *         url: string|null,
 *         label: string,
 *         page?: int|null,
 *         active: bool,
 *     }>,
 *     path: string,
 *     perPage: int,
 *     to: int|null,
 *     total: int,
 * }
 *
 * @phpstan-type CartItemInput array{
 *     productId: int,
 *     quantity?: int|null,
 * }
 *
 * @phpstan-type CheckoutOptions array{
 *     returnPath?: string|null,
 *     cartPath?: string|null,
 *     attribution?: array{
 *         utmSource?: string|null,
 *         utmMedium?: string|null,
 *         utmCampaign?: string|null,
 *         fbclid?: string|null,
 *         gclid?: string|null,
 *         gbraid?: string|null,
 *         wbraid?: string|null,
 *         dclid?: string|null,
 *         msclkid?: string|null,
 *         ttclid?: string|null,
 *         liFatId?: string|null,
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
 *     notifyCustomer?: bool|null,
 *     updatedAt?: UpdatedAtGuard,
 * }
 *
 * @phpstan-type OrderShipmentInput array{
 *     carrier: 'dhl'|'ups'|'fedex'|'aramex'|'smsa'|'dhl_ecommerce'|'babel_express'|'other',
 *     carrierName?: string|null,
 *     trackingNumber: string,
 *     trackingUrl?: string|null,
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
