<?php

namespace davidhirtz\yii2\cms\shopify\behaviors;

use davidhirtz\yii2\cms\models\base\ModelCloneEvent;
use davidhirtz\yii2\cms\models\Entry;
use davidhirtz\yii2\cms\shopify\validators\ProductIdValidator;
use davidhirtz\yii2\shopify\models\Product;
use davidhirtz\yii2\shopify\models\queries\ProductQuery;
use Yii;
use yii\base\Behavior;

/**
 * EntryProductBehavior extends {@see Entry} by providing 'product_id` validation. This behavior is attached on
 * bootstrap by {@see Bootstrap}.
 *
 * @property Entry $owner
 */
class EntryProductBehavior extends Behavior
{
    public function events(): array
    {
        return [
            Entry::EVENT_CREATE_VALIDATORS => 'onCreateValidators',
            Entry::EVENT_BEFORE_CLONE => 'onBeforeClone'
        ];
    }

    public function onCreateValidators(): void
    {
        $this->owner->getValidators()->append(new ProductIdValidator());
    }

    /**
     * @param ModelCloneEvent $event
     */
    public function onBeforeClone($event): void
    {
        Yii::debug('Setting product_id to null on cloned entry.', __METHOD__);
        $event->clone->setAttribute('product_id', null);
    }

    public function getProduct(): ProductQuery
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->owner->hasOne(Product::class, ['id' => 'product_id']);
    }
}