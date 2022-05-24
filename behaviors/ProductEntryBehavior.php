<?php

namespace davidhirtz\yii2\cms\shopify\behaviors;

use davidhirtz\yii2\cms\models\Entry;
use davidhirtz\yii2\cms\models\queries\EntryQuery;
use davidhirtz\yii2\cms\shopify\composer\Bootstrap;
use davidhirtz\yii2\shopify\models\Product;
use yii\base\Behavior;

/**
 * ProductEntryBehavior extends {@see Product} by updating related entries on delete. This behavior is attached on
 * bootstrap by {@see Bootstrap}.
 *
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

    /**
     * @return EntryQuery
     */
    public function getEntry()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->owner->hasOne(Entry::class, ['id' => 'product_id']);
    }
}