<?php

namespace davidhirtz\yii2\cms\shopify\behaviors;

use davidhirtz\yii2\cms\models\Entry;
use davidhirtz\yii2\cms\shopify\validators\ProductIdValidator;
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
}