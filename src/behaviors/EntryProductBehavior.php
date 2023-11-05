<?php

namespace davidhirtz\yii2\cms\shopify\behaviors;

use davidhirtz\yii2\cms\Bootstrap;
use davidhirtz\yii2\cms\models\ModelCloneEvent;
use davidhirtz\yii2\cms\models\Entry;
use davidhirtz\yii2\cms\shopify\validators\ProductIdValidator;
use davidhirtz\yii2\shopify\models\traits\ProductRelationTrait;
use Yii;
use yii\base\Behavior;

/**
 * EntryProductBehavior extends {@see Entry} by providing `product_id` validation. This behavior is attached on module
 * bootstrap by {@see Bootstrap}.
 *
 * @property Entry $owner
 */
class EntryProductBehavior extends Behavior
{
    use ProductRelationTrait;

    public function events(): array
    {
        return [
            Entry::EVENT_CREATE_VALIDATORS => $this->onCreateValidators(...),
            Entry::EVENT_BEFORE_CLONE => $this->onBeforeClone(...),
        ];
    }

    public function onCreateValidators(): void
    {
        $this->owner->getValidators()->append(new ProductIdValidator());
    }

    public function onBeforeClone(ModelCloneEvent $event): void
    {
        Yii::debug('Setting product_id to null on cloned entry.', __METHOD__);
        $event->clone->setAttribute('product_id', null);
    }
}