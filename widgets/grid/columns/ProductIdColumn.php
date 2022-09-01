<?php

namespace davidhirtz\yii2\cms\shopify\widgets\grid\columns;

use davidhirtz\yii2\cms\modules\admin\widgets\grid\EntryGridView;
use davidhirtz\yii2\shopify\models\Product;
use davidhirtz\yii2\skeleton\helpers\Html;
use Yii;
use yii\db\ActiveRecord;
use yii\grid\DataColumn;

/**
 * ProductIdColumn can be implemented by {@see EntryGridView} to add a product column.
 *
 * @property EntryGridView $grid
 */
class ProductIdColumn extends DataColumn
{
    /**
     * @var string
     */
    public $attribute = 'product_id';

    /**
     * @var Product[]
     */
    private static $_products;

    /**
     * @return void
     */
    public function init()
    {
        // Only show products if any are loaded on the page
        $this->visible = $this->visible && count($this->getProducts()) > 0;
        $this->label ??= Yii::t('shopify', 'Product');

        parent::init();
    }

    /**
     * @param ActiveRecord $model
     * @param string $key
     * @param int $index
     * @return string
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if ($product = ($this->getProducts()[$model->getAttribute($this->attribute)] ?? null)) {
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
                if ($productId = $model->getAttribute($this->attribute)) {
                    $productIds[] = $productId;
                }
            }

            if ($productIds) {
                static::$_products = Product::find()
                    ->select(['id', 'status', 'name'])
                    ->andWhere(['id' => $productIds])
                    ->indexBy('id')
                    ->all();
            }
        }

        return static::$_products;
    }
}
