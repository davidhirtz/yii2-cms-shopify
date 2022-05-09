README
============================

Shopify CMS backend based on the [Yii 2](http://www.yiiframework.com/) extensions [yii2-cms](https://github.com/davidhirtz/yii2-cms/)
and [yii2-shopify](https://github.com/davidhirtz/yii2-shopify/) by David Hirtz.

INSTALLATION
-------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

```
composer require davidhirtz/yii2-cms-shopify
```

Make sure to run the migrations after the installation via `php yii migrate`.

SETUP
-------------

- Use `davidhirtz\yii2\cms\shopify\widgets\forms\ProductIdFieldTrait` on `EntryActiveForm` and add `product_id` to fields
- Configure `EntryGridView` columns to use `davidhirtz\yii2\cms\shopify\widgets\grid\columns\ProductIdColumn` for
-

Behaviors for product and entry models will automatically attached at application bootstrap.

CONFIGURATION
-------------

### Shopify setup

Please follow the instructions in [README.md](https://github.com/davidhirtz/yii2-shopify/README.md) to setup the shopify
store.