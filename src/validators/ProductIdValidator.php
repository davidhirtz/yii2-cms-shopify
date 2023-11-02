<?php

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
    /**
     * @var array|string
     */
    public $attributes = ['product_id'];

    /**
     * @param Entry $model
     */
    public function validateAttribute($model, $attribute): void
    {
        if ($model->isAttributeChanged($attribute)) {
            $productId = $model->getAttribute($attribute);
            $isValid = Product::find()->where(['id' => $productId])->exists();

            if ($isValid) {
                $isTaken = Entry::find()
                    ->where(['product_id' => $productId])
                    ->andFilterWhere(['!=', 'id', $model->id])
                    ->exists();

                if ($isTaken) {
                    $model->addError($attribute, Yii::t('yii', '{attribute} "{value}" has already been taken.', [
                        'attribute' => Yii::t('shopify', 'Product'),
                    ]));
                }
            } else {
                $model->addInvalidAttributeError($attribute);
            }
        }
    }

    public function validate($value, &$error = null): bool
    {
        throw new NotSupportedException(get_class($this) . ' does not support validate().');
    }
}