<?php

declare(strict_types=1);

namespace davidhirtz\yii2\cms\shopify;

use davidhirtz\yii2\cms\models\Entry;
use davidhirtz\yii2\cms\modules\admin\widgets\forms\EntryActiveForm;
use davidhirtz\yii2\cms\shopify\behaviors\EntryProductBehavior;
use davidhirtz\yii2\cms\shopify\behaviors\ProductEntryBehavior;
use davidhirtz\yii2\cms\shopify\widgets\forms\ProductIdFieldBehavior;
use davidhirtz\yii2\shopify\models\Product;
use davidhirtz\yii2\skeleton\web\Application;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\Widget;
use yii\db\BaseActiveRecord;

class Bootstrap implements BootstrapInterface
{
    /**
     * @param Application $app
     */
    public function bootstrap($app): void
    {
        Event::on(Entry::class, Widget::EVENT_INIT, function (Event $event) {
            /** @var Entry $entry */
            $entry = $event->sender;
            $entry->attachBehavior('EntryProductBehavior', EntryProductBehavior::class);
        });

        Event::on(Product::class, BaseActiveRecord::EVENT_INIT, function (Event $event) {
            /** @var Product $product */
            $product = $event->sender;
            $product->attachBehavior('ProductEntryBehavior', ProductEntryBehavior::class);
        });

        Event::on(EntryActiveForm::class, BaseActiveRecord::EVENT_INIT, function (Event $event) {
            /** @var EntryActiveForm $form */
            $form = $event->sender;
            $form->attachBehavior('ProductIdFieldBehavior', ProductIdFieldBehavior::class);
        });

        $app->setMigrationNamespace('davidhirtz\yii2\cms\shopify\migrations');
    }
}
