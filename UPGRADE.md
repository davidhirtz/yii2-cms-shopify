# Upgrading to 3.0

## Requirements

- PHP `^8.3`
- `davidhirtz/yii2-cms` `^3.0` and `davidhirtz/yii2-shopify` `^3.0`, upgraded first: this bundle only adds a
  column, two behaviours and two admin widgets on top of them
- Nothing to install beyond `composer require davidhirtz/yii2-cms-shopify`; the bundle bootstraps itself through
  `extra.bootstrap` and ships no module, no messages and no console command

## Renames

Namespace and directories:

| v2 | v3 |
|---|---|
| `davidhirtz\yii2\cms\shopify\` | `Hirtz\Cms\Shopify\` |
| `src/behaviors/`, `src/models/`, `src/validators/`, `src/widgets/` | `src/Behaviors/`, `src/Models/`, `src/Validators/`, `src/Widgets/` |

Classes (relative to the namespace above):

| v2 | v3 |
|---|---|
| `behaviors\EntryProductBehavior` | `Behaviors\EntryProductBehavior` |
| `behaviors\ProductEntryBehavior` | `Behaviors\ProductEntryBehavior` |
| `models\Entry` | `Models\Entry` |
| `models\builders\EntrySiteRelationsBuilder` | `Events\ProductEntrySiteRelationsEventHandler` |
| `validators\ProductIdValidator` | `Validators\ProductIdValidator` |
| `widgets\forms\ProductIdFieldBehavior` | `Widgets\Forms\Fields\ProductIdSelectField` |
| `widgets\grids\columns\ProductIdColumn` | `Widgets\Grids\Columns\ProductIdColumn` |
| `migrations\M220506145159CmsShopify` | `Migrations\M260101000600CmsShopifyBaseline` (fresh installs only) |

Methods and properties:

| v2 | v3 |
|---|---|
| `EntrySiteRelationsBuilder::$autoloadVariants` | container definition `autoloadVariants` on `Events\ProductEntrySiteRelationsEventHandler` |
| `EntrySiteRelationsBuilder::loadProducts()`, `loadProductVariants()`, `getProductQuery()` | `ProductEntrySiteRelationsEventHandler::__invoke()` |
| `ProductIdFieldBehavior::productIdField()` | none: `Bootstrap` adds the field to the entry form |
| `ProductIdFieldBehavior::getProductIdItems()`, `getTakenProductIds()` | `ProductIdSelectField::getProductIdItems()`, `getTakenProductIds()` (protected) |
| `ProductIdFieldBehavior::$productIdPrompt` | `ProductIdSelectField::prompt()` (defaults to an empty first option) |
| `ProductIdColumn::$attribute` | constructor argument `$property` |
| `ProductIdColumn::$validateProductSlug` | removed, the slug is always compared |
| `ProductIdColumn::getProducts()` (public, static cache) | `getProducts()` (protected, per column) |
| `Models\Entry::attributeLabels()` | removed |

Message keys of the `shopify` category:

| v2 | v3 |
|---|---|
| `Yii::t('shopify', 'Product')` | `Yii::t('shopify', 'COMMON_PRODUCT')` |

The permission the bundle registers on the dashboard is `Hirtz\Shopify\Models\Product::AUTH_SHOPIFY_PRODUCT`
(`shopifyProduct`); the v2 name `shopifyProductUpdate` is renamed by the `davidhirtz/yii2-shopify` upgrade.

## Configuration

`autoloadVariants` moves from the builder's container definition to the event handler's:

```php
// v2
'container' => [
    'definitions' => [
        \davidhirtz\yii2\cms\shopify\models\builders\EntrySiteRelationsBuilder::class => [
            'autoloadVariants' => true,
        ],
    ],
],

// v3
'container' => [
    'definitions' => [
        \Hirtz\Cms\Shopify\Events\ProductEntrySiteRelationsEventHandler::class => [
            'autoloadVariants' => true,
        ],
    ],
],
```

A project that re-pointed `EntrySiteRelationsBuilder::class` to a subclass of its own has to move that code into a
listener of its own (see below); the bundle no longer registers anything under that key.

## Code changes

### The product field is added to the entry form by the bundle

`ProductIdFieldBehavior` is gone. `Bootstrap` listens for `Widget::EVENT_CONFIGURE` on
`Hirtz\Cms\Modules\Admin\Widgets\Forms\EntryActiveForm` and inserts `Widgets\Forms\Fields\ProductIdSelectField`
after the name field, so remove `product_id` from a project form's field list and every `productIdField()` call.
The field offers an empty first option; a project that wants the product required or a different label changes
the field from a listener or from `prepare()`, which runs after the bundle's listener:

```php
EventHelper::on(EntryActiveForm::class, Widget::EVENT_CONFIGURE, function (EntryActiveForm $form) {
    $form->rows(function (array $fieldsets): array {
        foreach ($fieldsets as $fieldset) {
            foreach ($fieldset->getRows() as $field) {
                if ($field instanceof ProductIdSelectField) {
                    $field->prompt(false);
                }
            }
        }

        return $fieldsets;
    });
});
```

### The product column is added to the entry grid by the bundle

`Bootstrap` inserts `Widgets\Grids\Columns\ProductIdColumn` after the name column of
`Hirtz\Cms\Modules\Admin\Widgets\Grids\EntryGridView`, so remove it from a project grid's `columns()` or it
renders twice. The column hides itself while no entry on the page carries a product; `$validateProductSlug` is
gone and the warning icon for a slug differing from the product's is always rendered. The products a column
loaded are kept on the column, not in a static, so two entry grids in one request no longer share them.

### The site relations builder is an event handler

`models\builders\EntrySiteRelationsBuilder` is replaced by `Events\ProductEntrySiteRelationsEventHandler`, an
invokable listening for `Hirtz\Cms\Models\Actions\PreloadEntrySiteRelations::EVENT_AFTER_LOAD_ENTRIES`. It
populates the `product` relation of every loaded entry from one query (`with('variant')`, or `with('variants')`
with `autoloadVariants`) and reads `product_id` through `getAttribute()`, so an entry whose column is `null` is
fine. A project that extended the builder registers a listener of its own:

```php
Event::on(
    PreloadEntrySiteRelations::class,
    PreloadEntrySiteRelations::EVENT_AFTER_LOAD_ENTRIES,
    function (EntrySiteRelationsEvent $event) {
        foreach ($event->sender->entries as $entry) {
            // ...
        }
    }
);
```

### The `product` relation stays a project decision

`Models\Entry` is still the example model: `Hirtz\Cms\Models\Entry` plus
`Hirtz\Shopify\Models\Traits\ProductRelationTrait` and a `@property int|null $product_id` docblock. Its v2
`attributeLabels()` override is gone, because the field and the column carry the label themselves. A project
either re-points `Hirtz\Cms\Models\Entry::class` to it in the container or uses the trait on its own `Entry`.
The trait declares `getProduct()` and `populateProductRelation()` and no longer declares the column, so the
using model declares `product_id` itself.

### Behaviours and validator

`Behaviors\EntryProductBehavior` (attached to every `Hirtz\Cms\Models\Entry` as `EntryProductBehavior`: appends
`Validators\ProductIdValidator` and clears `product_id` on duplication) and `Behaviors\ProductEntryBehavior`
(attached to every `Hirtz\Shopify\Models\Product` as `ProductEntryBehavior`: invalidates the cms page cache on
save, disables and unlinks the entry on delete) behave as in v2 under their new namespace. The validator's
"already taken" message names the product through `COMMON_PRODUCT`.

## Data and schema

`davidhirtz/yii2-upgrade` carries no migration for this bundle (there is no `migrations/yii2-cms-shopify/`
directory): `entry.product_id`, its unique index `product_id` and the foreign key `entry_product_id_ibfk` on
`product.id` (`ON DELETE SET NULL`) were created by the v2 migration `M220506145159CmsShopify` and are exactly
what `Migrations\M260101000600CmsShopifyBaseline` creates on a fresh install. The upgrade tool's collapse step
replaces the v2 history row with the baseline's; the baseline itself must never run against a v2 database, and
its `safeDown()` refuses rather than dropping the column.

What touches the product side lives in the `davidhirtz/yii2-shopify` upgrade (`migrations/yii2-shopify/`): the
translated product columns move into the `translation` table and `shopifyProductUpdate` becomes
`shopifyProduct`. Run the cms and shopify upgrades in their documented order; nothing here needs a
`search/rebuild` or a `permalink/rebuild` of its own.

A v2 project using the cms's per-language entry tables had one `product_id` column per table; what becomes of
those tables is the `davidhirtz/yii2-cms` upgrade's concern.

## Removed

- `Widgets\Forms\ProductIdFieldBehavior`, and with it the option of leaving the product field out of the entry
  form by omitting `product_id` from the field list; drop the field from a listener instead.
- `ProductIdColumn::$validateProductSlug`: the slug comparison cannot be turned off.
- `Models\Entry::attributeLabels()` with its `product_id` label.
