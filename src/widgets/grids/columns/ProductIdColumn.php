<?php

declare(strict_types=1);

namespace davidhirtz\yii2\cms\shopify\widgets\grids\columns;

use davidhirtz\yii2\cms\models\ActiveRecord;
use davidhirtz\yii2\cms\modules\admin\widgets\grids\EntryGridView;
use davidhirtz\yii2\shopify\models\Product;
use davidhirtz\yii2\skeleton\helpers\Html;
use Yii;
use yii\grid\DataColumn;

/**
 * @property EntryGridView $grid
 */
class ProductIdColumn extends DataColumn
{
    /**
     * @var string
     */
    public $attribute = 'product_id';

    /**
     * @var bool whether mismatching product URLs should be marked with a marking
     */
    public bool $validateProductSlug = true;

    protected static ?array $_products = null;

    public function init(): void
    {
        $this->label ??= Yii::t('shopify', 'Product');
        $this->visible = $this->visible && count($this->getProducts()) > 0;

        parent::init();
    }

    /**
     * @param ActiveRecord $model
     */
    protected function renderDataCellContent($model, $key, $index): string
    {
        $product = ($this->getProducts()[$model->getAttribute($this->attribute)] ?? null);

        if (!$product) {
            return '';
        }

        if ($product->status == $model->status) {
            $name = $this->validateProductSlug && $product->slug != $model->getI18nAttribute('slug')
                ? $this->getNameWithSlugWarning($model, $product)
                : Html::encode($product->name);
        } else {
            $name = $product->status < $model->status
                ? $this->getNameWithStatusIcon($model, $product)
                : Html::encode($product->name);
        }

        return Html::a($name, $product->getShopifyAdminUrl(), [
            'target' => '_blank',
        ]);
    }

    protected function getNameWithSlugWarning(ActiveRecord $model, Product $product): string
    {
        return Html::iconText('exclamation-triangle', Html::encode($product->name), [
            'title' => Yii::t('yii', '{attribute} must be equal to "{compareValueOrAttribute}".', [
                'attribute' => $model->getAttributeLabel('slug'),
                'compareValueOrAttribute' => $product->slug,
            ]),
            'data-toggle' => 'tooltip',
        ]);
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     */
    protected function getNameWithStatusIcon(ActiveRecord $model, Product $product): string
    {
        return Html::iconText($product->getStatusIcon(), Html::encode($product->name), [
            'title' => $product->getStatusName(),
            'data-toggle' => 'tooltip',
        ]);
    }

    /**
     * @return Product[]
     */
    public function getProducts(): array
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
                    ->select(['id', 'status', 'name', 'slug'])
                    ->andWhere(['id' => $productIds])
                    ->indexBy('id')
                    ->all();
            }
        }

        return static::$_products;
    }
}
