Shopify CMS backend based on the [Yii 2](http://www.yiiframework.com/) extensions [yii2-cms](https://github.com/davidhirtz/yii2-cms/)
and [yii2-shopify](https://github.com/davidhirtz/yii2-shopify/).

- Make sure `app\modules\admin\widgets\forms\EntryActiveForm::$fields` includes `product_id` to display the entries drop down
- [Optional] Use `davidhirtz\yii2\cms\shopify\widgets\grids\columns\ProductIdColumn` to display an entries counter in `davidhirtz\yii2\cms\modules\admin\widgets\grid\EntryGridView`

See also the instructions in the [yii2-shopify](https://github.com/davidhirtz/yii2-shopify/) package to set up the Shopify store.