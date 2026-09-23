# yii2-cms-shopify

Links the entries of [yii2-cms](https://github.com/davidhirtz/yii2-cms/) to the products
[yii2-shopify](https://github.com/davidhirtz/yii2-shopify/) syncs from a Shopify store: an entry may stand for
exactly one product through `entry.product_id`, the admin offers the product in the entry form and grid, and
the frontend gets the product (and its variant) preloaded beside the entry's other relations. It requires both
bundles and adds nothing of its own beyond that: no module, no controller, no message files and no console
command.

## Installation

```bash
composer require davidhirtz/yii2-cms-shopify
./yii migrate
```

The bundle bootstraps itself through `extra.bootstrap` (`Hirtz\Cms\Shopify\Bootstrap`); nothing is added to the
application config. The migration adds `entry.product_id` with a unique index and a foreign key on `product.id`
(`ON DELETE SET NULL`). Set up the Shopify store and its webhooks as described in the yii2-shopify README; the
products have to be synced before an entry can be linked to one.

## Configuration

There is no module, so nothing under `modules`. The bundle reads no params of its own; the Shopify credentials
(`shopifyShopName`, `shopifyApiKey`, `shopifyApiSecret`, `shopifyAccessToken`, `shopifyStorefrontAccessToken`)
belong to yii2-shopify.

The one option is a container definition on the site relations handler. By default the preload eager loads each
product's default `variant`; with `autoloadVariants` it loads every `variants` row and populates `variant` from
them:

```php
'container' => [
    'definitions' => [
        \Hirtz\Cms\Shopify\Events\ProductEntrySiteRelationsEventHandler::class => [
            'autoloadVariants' => true,
        ],
    ],
],
```

`Models\Entry` is an example model: `Hirtz\Cms\Models\Entry` with `Hirtz\Shopify\Models\Traits\ProductRelationTrait`,
which gives the entry its `product` relation (`getProduct()`, `populateProductRelation()`). A project either
re-points the cms entry to it or uses the trait on its own `Entry`, declaring `@property int|null $product_id`:

```php
'container' => [
    'definitions' => [
        \Hirtz\Cms\Models\Entry::class => \Hirtz\Cms\Shopify\Models\Entry::class,
    ],
],
```

## What the bootstrap wires

Everything happens from `Bootstrap`, through events on the cms and shopify classes:

- `Behaviors\EntryProductBehavior` is attached to every `Hirtz\Cms\Models\Entry` on `EVENT_INIT`. It appends
  `Validators\ProductIdValidator` to the entry's validators (the product must exist and be linked to no other
  entry; an empty value is normalized to `null`) and clears `product_id` before an entry is duplicated.
- `Behaviors\ProductEntryBehavior` is attached to every `Hirtz\Shopify\Models\Product`. Saving a product
  invalidates the cms page cache; deleting one disables the entry linked to it and sets its `product_id` to
  `null`.
- `Events\ProductEntrySiteRelationsEventHandler` listens for
  `Hirtz\Cms\Models\Actions\PreloadEntrySiteRelations::EVENT_AFTER_LOAD_ENTRIES` and populates the `product`
  relation of every loaded entry from one query, filtered with `whereStatus()` at the status the cms site
  controller pinned for the request, so a site view never lazy loads it.
- `Widgets\Forms\Fields\ProductIdSelectField` is inserted after the name field of
  `Hirtz\Cms\Modules\Admin\Widgets\Forms\EntryActiveForm`, and `Widgets\Grids\Columns\ProductIdColumn` after
  the name column of `Hirtz\Cms\Modules\Admin\Widgets\Grids\EntryGridView`, both through
  `Widget::EVENT_CONFIGURE`. The listener runs after the widget's own defaults and before the caller's
  `prepare()`, so a project still has the last word. The select lists every product not taken by another entry,
  disabled ones prefixed with their status, and its empty first option is how an entry is unlinked again. The
  column links the product name to the Shopify admin, shows the product's status icon where it differs from
  the entry's and warns where the slugs differ, and hides itself while no entry on the page carries a product.
- `Hirtz\Shopify\Models\Product::AUTH_SHOPIFY_PRODUCT` is added to the roles the admin dashboard lists.

The labels come from the `shopify` message category (`COMMON_PRODUCT`).

## Console commands

None.
