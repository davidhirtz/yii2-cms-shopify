<?php

declare(strict_types=1);

namespace davidhirtz\yii2\cms\shopify\behaviors;

use davidhirtz\yii2\cms\models\Entry;
use davidhirtz\yii2\shopify\models\Product;
use davidhirtz\yii2\cms\models\traits\EntryRelationTrait;
use davidhirtz\yii2\cms\Module;
use davidhirtz\yii2\cms\shopify\Bootstrap;
use Yii;
use yii\base\Behavior;

/**
 * ProductEntryBehavior extends {@see Product} by updating related entries on deletion. This behavior is attached on
 * bootstrap by {@see Bootstrap}.
 *
 * @property Product $owner
 */
class ProductEntryBehavior extends Behavior
{
    use EntryRelationTrait;

    public function events(): array
    {
        return [
            Product::EVENT_AFTER_INSERT => $this->onAfterSave(...),
            Product::EVENT_AFTER_UPDATE => $this->onAfterSave(...),
            Product::EVENT_BEFORE_DELETE => $this->onBeforeDelete(...),
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
            if ($entry->isEnabled()) {
                $entry->status = Entry::STATUS_DISABLED;
            }

            $entry->setAttribute('product_id', null);
            $entry->update();
        }
    }
}
