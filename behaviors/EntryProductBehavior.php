<?php

namespace davidhirtz\yii2\cms\shopify\behaviors;

use davidhirtz\yii2\cms\models\Entry;
use davidhirtz\yii2\cms\shopify\validators\ProductIdValidator;
use davidhirtz\yii2\shopify\models\Product;
use davidhirtz\yii2\shopify\models\queries\ProductQuery;
use yii\base\Behavior;

/**
 * EntryProductBehavior extends {@see Entry} by providing 'product_id` validation. This behavior is attached on
 * bootstrap by {@see Bootstrap}.
 *
 * @property Entry $owner
 */
class EntryProductBehavior extends Behavior
{
    /**
     * @return string[]
     */
    public function events(): array
    {
        return [
            Entry::EVENT_CREATE_VALIDATORS => 'onCreateValidators',
        ];
    }

    /**
     * @return void
     */
    public function onCreateValidators()
    {
        $this->owner->getValidators()->append(new ProductIdValidator());
    }

    /**
     * @return ProductQuery
     */
    public function getProduct()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->owner->hasOne(Product::class, ['id' => 'product_id']);
    }
}