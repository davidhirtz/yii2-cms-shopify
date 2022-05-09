<?php

namespace davidhirtz\yii2\cms\shopify\behaviors;

use davidhirtz\yii2\cms\models\Entry;
use davidhirtz\yii2\shopify\models\Product;
use yii\base\Behavior;

/**
 * @property Product $owner
 */
class ProductEntryBehavior extends Behavior
{
    /**
     * @return string[]
     */
    public function events(): array
    {
        return [
            Product::EVENT_BEFORE_DELETE => 'onBeforeDelete',
        ];
    }

    /**
     * @return void
     */
    public function onBeforeDelete()
    {
        if ($entry = Entry::findOne(['product_id' => $this->owner->id])) {
            $entry->status = Entry::STATUS_DISABLED;
            $entry->update();
        }
    }
}