<?php

namespace davidhirtz\yii2\cms\shopify\behaviors;

use davidhirtz\yii2\cms\Bootstrap;
use davidhirtz\yii2\cms\models\Entry;
use davidhirtz\yii2\cms\shopify\validators\ProductIdValidator;
use davidhirtz\yii2\shopify\models\traits\ProductRelationTrait;
use davidhirtz\yii2\skeleton\models\actions\DuplicateActiveRecord;
use davidhirtz\yii2\skeleton\models\events\DuplicateActiveRecordEvent;
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
            DuplicateActiveRecord::EVENT_BEFORE_DUPLICATE => $this->onBeforeClone(...),
        ];
    }

    public function onCreateValidators(): void
    {
        $this->owner->getValidators()->append(new ProductIdValidator());
    }

    public function onBeforeClone(DuplicateActiveRecordEvent $event): void
    {
        if ($event->duplicate->getAttribute('product_id')) {
            Yii::debug('Removing product_id before duplicating entry.', __METHOD__);
            $event->duplicate->setAttribute('product_id', null);
        }
    }
}
