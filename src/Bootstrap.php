<?php

declare(strict_types=1);

namespace davidhirtz\yii2\cms\shopify;

use davidhirtz\yii2\cms\models\builders\EntrySiteRelationsBuilder;
use davidhirtz\yii2\cms\models\Entry;
use davidhirtz\yii2\cms\modules\admin\widgets\forms\EntryActiveForm;
use davidhirtz\yii2\cms\shopify\behaviors\EntryProductBehavior;
use davidhirtz\yii2\cms\shopify\behaviors\ProductEntryBehavior;
use davidhirtz\yii2\cms\shopify\widgets\forms\ProductIdFieldBehavior;
use davidhirtz\yii2\shopify\models\Product;
use davidhirtz\yii2\skeleton\web\Application;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;

class Bootstrap implements BootstrapInterface
{
    /**
     * @param Application $app
     */
    public function bootstrap($app): void
    {
        Event::on(Entry::class, Entry::EVENT_INIT, function (Event $event) {
            /** @var Entry $entry */
            $entry = $event->sender;
            $entry->attachBehavior('EntryProductBehavior', EntryProductBehavior::class);
        });

        Event::on(Product::class, Product::EVENT_INIT, function (Event $event) {
            /** @var Product $product */
            $product = $event->sender;
            $product->attachBehavior('ProductEntryBehavior', ProductEntryBehavior::class);
        });

        Event::on(EntryActiveForm::class, EntryActiveForm::EVENT_INIT, function (Event $event) {
            /** @var EntryActiveForm $form */
            $form = $event->sender;
            $form->attachBehavior('ProductIdFieldBehavior', ProductIdFieldBehavior::class);
        });

        if (!Yii::$container->has(EntrySiteRelationsBuilder::class)) {
            Yii::$container->set(EntrySiteRelationsBuilder::class, models\builders\EntrySiteRelationsBuilder::class);
        }

        $app->setMigrationNamespace('davidhirtz\yii2\cms\shopify\migrations');
    }
}
