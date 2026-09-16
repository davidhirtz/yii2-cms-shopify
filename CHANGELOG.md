## 3.0.0 (in development)

- **`Events\ProductEntrySiteRelationsBuilderEventHandler` is `Events\ProductEntrySiteRelationsEventHandler`**,
  following the cms rename of `Models\Builders\EntrySiteRelationsBuilder` to
  `Models\Actions\PreloadEntrySiteRelations` (monorepo issue #136). The handler reads `autoloadVariants` off its
  own container definition through `self::class`, so a project setting that option renames the key with it:

  ```php
  'container' => [
      'definitions' => [
          ProductEntrySiteRelationsEventHandler::class => ['autoloadVariants' => true],
      ],
  ],
  ```

- `Events\ProductEntrySiteRelationsEventHandler` no longer answers a 500 for an entry whose `product_id`
  or `variant_id` is empty, which is the default: both were array offsets, and `null` is not a legal one. Its
  `reset()` also read a relation by reference, which is an `Indirect modification of overloaded property`
  notice (monorepo issue #129).

- `Bootstrap` places the product field through the fieldset that holds the entry's name field, rather than guessing
  at the shape of `ActiveForm::$rows`. Behaviour is unchanged; the skeleton normalizes the rows now (monorepo issue
  #120).

- **`Bootstrap` adds the product field and column to the cms entry admin itself.** A
  `Widget::EVENT_CONFIGURE` listener puts `Widgets\Forms\Fields\ProductIdSelectField` and
  `Widgets\Grids\Columns\ProductIdColumn` directly after the entry's name field and name column, so an entry is
  linked to a product without a project subclassing `EntryActiveForm` and `EntryGridView`. The listener runs after
  the widget's own defaults and before the caller's `prepare()`, so a project still has the last word
- `ProductIdSelectField` offers an empty first option: an entry does not have to stand for a product, and it is
  how one is unlinked again
- `ProductIdColumn` decides its visibility in a closure rather than in its constructor. It counted the products of
  the grid's provider before the grid was bound to it, so building the column threw whatever added it
- `Widgets\Grids\Columns\ProductIdColumn` keeps the products it loaded on the column rather than in a static,
  and `ProductIdColumn::reset()` is gone with it, as is the `Bootstrap` call that cleared it. The static was keyed
  to nothing, so a second entry grid in the same request reported the products of the first — and a first grid
  holding none hid the column for every grid after it

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