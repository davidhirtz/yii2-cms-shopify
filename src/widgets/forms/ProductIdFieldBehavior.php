<?php

namespace davidhirtz\yii2\cms\shopify\widgets\forms;

use davidhirtz\yii2\cms\models\Entry;
use davidhirtz\yii2\cms\modules\admin\widgets\forms\EntryActiveForm;
use davidhirtz\yii2\cms\shopify\composer\Bootstrap;
use davidhirtz\yii2\shopify\models\Product;
use Yii;
use yii\base\Behavior;
use yii\widgets\ActiveField;

/**
 * ProductIdFieldBehavior extends {@see EntryActiveForm} to add a product select field. It only shows products that
 * are not already linked to another entry.  This behavior is attached on startup by {@see Bootstrap}.
 *
 * All methods can be overridden in the form class to customize the behavior.
 *
 * @property EntryActiveForm $owner
 */
class ProductIdFieldBehavior extends Behavior
{
    /**
     * @var string|array|null set to `null` to require product selection, defaults to empty for the first option
     */
    public string|array|null $productIdPrompt = '';

    public function productIdField(array $options = []): ActiveField|string
    {
        /** @var static $form */
        $form = $this->owner;

        if (count($items = $form->getProductIdItems())) {
            return $this->owner->field($this->owner->model, 'product_id', $options)
                ->label(Yii::t('shopify', 'Product'))
                ->dropdownList($items, ['prompt' => $form->productIdPrompt]);
        }

        return '';
    }

    /**
     * @see static::productIdField()
     */
    public function getProductIdItems(): array
    {
        /** @var static $form */
        $form = $this->owner;
        $takenProductIds = $form->getTakenProductIds();

        $products = Product::find()
            ->select(['id', 'status', 'name'])
            ->filterWhere(['not in', 'id', $takenProductIds])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        $items = [];

        foreach ($products as $product) {
            $items[$product->id] = !$product->isEnabled()
                ? ('[' . $product->getStatusName() . "] $product->name")
                : $product->name;
        }

        return $items;
    }

    public function getTakenProductIds(): array
    {
        return Entry::find()
            ->select('product_id')
            ->where(['IS NOT', 'product_id', null])
            ->andFilterWhere(['!=', 'id', $this->owner->model->id])
            ->column();
    }
}