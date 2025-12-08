<?php

declare(strict_types=1);

namespace Hirtz\Cms\shopify\behaviors;

use Hirtz\Cms\Models\Entry;
use Hirtz\Cms\Models\traits\EntryRelationTrait;
use Hirtz\Cms\Module;
use Hirtz\Cms\shopify\Bootstrap;
use Hirtz\Shopify\models\Product;
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
            $entry->status = Entry::STATUS_DISABLED;
            $entry->update();
        }
    }
}
