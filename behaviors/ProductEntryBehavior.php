<?php

namespace davidhirtz\yii2\cms\shopify\behaviors;

use davidhirtz\yii2\cms\models\Entry;
use davidhirtz\yii2\cms\models\queries\EntryQuery;
use davidhirtz\yii2\cms\Module;
use davidhirtz\yii2\cms\shopify\composer\Bootstrap;
use davidhirtz\yii2\shopify\models\Product;
use Yii;
use yii\base\Behavior;

/**
 * ProductEntryBehavior extends {@see Product} by updating related entries on delete. This behavior is attached on
 * bootstrap by {@see Bootstrap}.
 *
 * @property Product $owner
 */
class ProductEntryBehavior extends Behavior
{
    public function events(): array
    {
        return [
            Product::EVENT_AFTER_INSERT => 'onAfterSave',
            Product::EVENT_AFTER_UPDATE => 'onAfterSave',
            Product::EVENT_BEFORE_DELETE => 'onBeforeDelete',
        ];
    }

    public function onAfterSave(): void
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('cms');
        $module->invalidatePageCache();
    }

    public function onBeforeDelete(): void
    {
        if ($entry = Entry::findOne(['product_id' => $this->owner->id])) {
            $entry->status = Entry::STATUS_DISABLED;
            $entry->update();
        }
    }

    public function getEntry(): EntryQuery
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->owner->hasOne(Entry::class, ['id' => 'product_id']);
    }
}