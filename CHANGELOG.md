## 2.2.0 (in development)

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
- Changed namespace of `davidhirtz\yii2\cms\shopify\widgets\grid\columns\ProductIdColumn`
  to `davidhirtz\yii2\cms\shopify\widgets\grids\columns\ProductIdColumn`
- Upgraded `davidhirtz/yii2-shopify` to version `^2.0`

## 1.0.8 (Nov 2, 2023)

- Locked `davidhirtz/yii2-shopify` to version `^1.1`, upgrade to version 2.0 to use the latest version of this package