## Unreleased

- Changed the requirements to `davidhirtz/yii2-cms` `^3.10`, `davidhirtz/yii2-shopify` `^3.1` and
  `davidhirtz/yii2-skeleton` `^3.8`
- Fixed deleting a product leaving its entry enabled when the entry no longer validates

## 3.1.0 (September 29, 2026)

- Requires `davidhirtz/yii2-skeleton` `^3.6`, whose widget options are protected

## 3.0.0 (September 23, 2026)

- Renamed the namespace `davidhirtz\yii2\cms\shopify\` to `Hirtz\Cms\Shopify\` and every directory under `src/` to
  StudlyCase (`behaviors` → `Behaviors`, `widgets\grids\columns` → `Widgets\Grids\Columns`); requires PHP 8.3,
  `davidhirtz/yii2-cms` 3.0 and `davidhirtz/yii2-shopify` 3.0
- Removed `Widgets\Forms\ProductIdFieldBehavior` with `productIdField()`, `getProductIdItems()`,
  `getTakenProductIds()` and `$productIdPrompt`; `Bootstrap` inserts `Widgets\Forms\Fields\ProductIdSelectField`
  after the name field of `Hirtz\Cms\Modules\Admin\Widgets\Forms\EntryActiveForm` through
  `Widget::EVENT_CONFIGURE`, so a project no longer lists `product_id` in its form
- Replaced `Widgets\Grids\Columns\ProductIdColumn` (a `yii\grid\DataColumn` with `$attribute`,
  `$validateProductSlug` and a static product cache) with a `Hirtz\Skeleton\Widgets\Grids\Columns\Column` taking
  the property in its constructor; `Bootstrap` inserts it after the name column of
  `Hirtz\Cms\Modules\Admin\Widgets\Grids\EntryGridView`, visible only while the page lists a product, and the
  slug warning is always shown
- Replaced `Models\Builders\EntrySiteRelationsBuilder` with `Events\ProductEntrySiteRelationsEventHandler`, a
  listener on `Hirtz\Cms\Models\Actions\PreloadEntrySiteRelations::EVENT_AFTER_LOAD_ENTRIES`; `$autoloadVariants`
  is now the `autoloadVariants` key of the handler's container definition
- Removed `Models\Entry::attributeLabels()`; the product field and column take their label from the `shopify`
  message key `COMMON_PRODUCT`, which replaces `Yii::t('shopify', 'Product')` in `Validators\ProductIdValidator`
  as well
- Added `Hirtz\Shopify\Models\Product::AUTH_SHOPIFY_PRODUCT` to the dashboard roles through
  `DashboardController::addRoles()`
- Replaced `Migrations\M220506145159CmsShopify` with the fresh-install baseline
  `Migrations\M260101000600CmsShopifyBaseline`, which cannot be reverted

## 2.2.2 (Jan 26, 2026)

- PHP 8.5 compatibility fixes

## 2.2.1 (Jan 26, 2026)

- PHP 8.5 compatibility fixes

## 2.2.0 (Jul 28, 2025)

- Added `Entry::$product_id` label
- Enhanced `ProductIdValidator` error message to include the product name
- Enhanced `ProductIdColumn` to display the status icon only if the product status is less than the related product

## 2.1.10 (Mar 26, 2025)

- Added `ProductRelationTrait` to `Entry` and removed it from `EntryProductBehavior`
- Fixed default variant population in `EntrySiteRelationsBuilder`

## 2.1.9 (Mar 24, 2025)

- Added `EntrySiteRelationsBuilder` config in `Bootstrap` class
- Fixed bug on empty product relation

## 2.1.8 (Mar 24, 2025)

- Added example `Entry` model
- Added `EntrySiteRelationsBuilder::$autoloadVariants` option

## 2.1.7 (Mar 20, 2024)

- Enhanced `ProductIdValidator` to validate product ID only if it's attribute is visible

## 2.1.6 (Jan 28, 2024)

- Updated dependencies

## 2.1.5 (Nov 29, 2024)

- Fixed migration for I18N entry tables

## 2.1.4 (Sep 19, 2024)

- Enabled `ProductIdValidator` initialization via container
- Improved `ProductIdValidator` to stop validation if the product ID was not changed

## 2.1.3 (Mar 30, 2024)

- Added `EntrySiteRelationsBuilder`

## 2.1.2 (Feb 1, 2023)

- Updated `EntryProductBehavior` to make use of new `CreateValidatorsEvent` event

## 2.1.1 (Jan 29, 2023)

- Updated dependencies

## 2.1.0 (Dec 20, 2023)

- Added Codeception test suite
- Added GitHub Actions CI workflow

## 2.0.1 (Nov 6, 2023)

-Moved `Bootstrap` class to base package namespace for consistency

## 2.0.0 (Nov 3, 2023)

- Moved source code to `src` folder
- Changed namespace of `Hirtz\Cms\Shopify\widgets\grid\columns\ProductIdColumn`
  to `Hirtz\Cms\Shopify\Widgets\Grids\columns\ProductIdColumn`
- Upgraded `davidhirtz/yii2-shopify` to version `^2.0`

## 1.0.8 (Nov 2, 2023)

- Locked `davidhirtz/yii2-shopify` to version `^1.1`, upgrade to version 2.0 to use the latest version of this package
