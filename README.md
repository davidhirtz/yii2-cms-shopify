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

- Make sure `app\modules\admin\widgets\forms\EntryActiveForm::$fields` includes `product_id` to display the entries drop down
- [Optional] Use `davidhirtz\yii2\cms\shopify\widgets\grid\columns\ProductIdColumn` to display an entries counter in `davidhirtz\yii2\cms\modules\admin\widgets\grid\EntryGridView`

CONFIGURATION
-------------

### Shopify setup

Please follow the instructions in [README.md](https://github.com/davidhirtz/yii2-shopify/README.md) to setup the shopify
store.