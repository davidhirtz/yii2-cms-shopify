<?php

declare(strict_types=1);

namespace Hirtz\Cms\Shopify\Behaviors;

use Hirtz\Cms\Models\Entry;
use Hirtz\Cms\Models\Traits\EntryRelationTrait;
use Hirtz\Cms\Module;
use Hirtz\Cms\Shopify\Bootstrap;
use Hirtz\Shopify\Models\Product;
use Override;
use Yii;
use yii\base\Behavior;

/**
 * ProductEntryBehavior extends {@see Product} by updating related entries on deletion. This behavior is attached on
 * bootstrap by {@see Bootstrap}.
 *
 * @extends Behavior<Product>
 */
class ProductEntryBehavior extends Behavior
{
    use EntryRelationTrait;

    #[Override]
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
