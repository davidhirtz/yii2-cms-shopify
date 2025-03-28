<?php

declare(strict_types=1);

namespace davidhirtz\yii2\cms\shopify\validators;

use davidhirtz\yii2\cms\models\Entry;
use davidhirtz\yii2\cms\shopify\behaviors\EntryProductBehavior;
use davidhirtz\yii2\shopify\models\Product;
use Yii;
use yii\base\NotSupportedException;
use yii\validators\Validator;

/**
 * ProductIdValidator validates the entry's `product_id`. The validator is automatically added to the model's validators
 * by {@see EntryProductBehavior}.
 */
class ProductIdValidator extends Validator
{
    public $attributes = ['product_id'];
    public $skipOnEmpty = false;

    /**
     * @param Entry $model
     */
    public function validateAttribute($model, $attribute): void
    {
        $productId = (int)$model->getVisibleAttribute($attribute) ?: null;
        $model->setAttribute($attribute, $productId);

        if (!$model->isAttributeChanged($attribute) || $productId === null) {
            return;
        }

        $product = Product::findOne($productId);

        if (!$product) {
            $model->addInvalidAttributeError($attribute);
            return;
        }

        $isTaken = Entry::find()
            ->where(['product_id' => $productId])
            ->andFilterWhere(['!=', 'id', $model->id])
            ->exists();

        if ($isTaken) {
            $model->addError($attribute, Yii::t('yii', '{attribute} "{value}" has already been taken.', [
                'attribute' => Yii::t('shopify', 'Product'),
                'value' => $product->name,
            ]));
        }
    }

    public function validate($value, &$error = null): bool
    {
        throw new NotSupportedException(static::class . ' does not support validate().');
    }
}
