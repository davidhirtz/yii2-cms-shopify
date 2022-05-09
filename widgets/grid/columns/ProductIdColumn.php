<?php

namespace davidhirtz\yii2\cms\shopify\widgets\grid\columns;

use davidhirtz\yii2\cms\modules\admin\widgets\grid\EntryGridView;
use davidhirtz\yii2\shopify\models\Product;
use davidhirtz\yii2\skeleton\helpers\Html;
use yii\db\ActiveRecord;
use yii\grid\Column;

/**
 * ProductIdColumn can be implemented by {@see EntryGridView} to add a product column.
 *
 * @property EntryGridView $grid
 */
class ProductIdColumn extends Column
{
    /**
     * @var Product[]
     */
    private static $_products;

    /**
     * @param ActiveRecord $model
     * @param string $key
     * @param int $index
     * @return string
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if ($product = ($this->getProducts()[$model->$key] ?? null)) {
            $name = $product->status == $model->getAttribute('status') ? $product->name : Html::iconText($product->getStatusIcon(), $product->name, [
                'title' => $product->getStatusName(),
                'data-toggle' => 'tooltip',
            ]);

            return Html::a($name, $product->getAdminRoute());
        }

        return '';
    }

    /**
     * @return Product[]
     */
    public function getProducts()
    {
        if (static::$_products === null) {
            static::$_products = [];
            $productIds = [];

            foreach ($this->grid->dataProvider->getModels() as $model) {
                if ($productId = $model->getAttribute('id')) {
                    $productIds[] = $productId;
                }
            }

            if ($productIds) {
                static::$_products = Product::find()
                    ->select(['id', 'status', 'name'])
                    ->indexBy('name')
                    ->all();
            }
        }

        return static::$_products;
    }
}
