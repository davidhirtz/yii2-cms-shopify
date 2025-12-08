<?php

declare(strict_types=1);

namespace Hirtz\Cms\shopify\behaviors;

use Hirtz\Cms\Bootstrap;
use Hirtz\Cms\models\Entry;
use Hirtz\Cms\shopify\validators\ProductIdValidator;
use Hirtz\Shopify\models\traits\ProductRelationTrait;
use Hirtz\Skeleton\models\actions\DuplicateActiveRecord;
use Hirtz\Skeleton\models\events\CreateValidatorsEvent;
use Hirtz\Skeleton\models\events\DuplicateActiveRecordEvent;
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
            CreateValidatorsEvent::EVENT_CREATE_VALIDATORS => $this->onCreateValidators(...),
            DuplicateActiveRecord::EVENT_BEFORE_DUPLICATE => $this->onBeforeDuplicate(...),
        ];
    }

    public function onCreateValidators(CreateValidatorsEvent $event): void
    {
        $event->validators->append(Yii::createObject(ProductIdValidator::class));
    }

    public function onBeforeDuplicate(DuplicateActiveRecordEvent $event): void
    {
        if ($event->duplicate->getAttribute('product_id')) {
            Yii::debug('Removing product_id before duplicating entry.', __METHOD__);
            $event->duplicate->setAttribute('product_id', null);
        }
    }
}
