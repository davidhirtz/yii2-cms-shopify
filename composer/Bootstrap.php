<?php

namespace davidhirtz\yii2\cms\shopify\composer;

use davidhirtz\yii2\cms\models\Entry;
use davidhirtz\yii2\cms\modules\admin\widgets\forms\EntryActiveForm;
use davidhirtz\yii2\cms\shopify\behaviors\EntryProductBehavior;
use davidhirtz\yii2\cms\shopify\behaviors\ProductEntryBehavior;
use davidhirtz\yii2\cms\shopify\widgets\forms\ProductIdFieldTrait;
use davidhirtz\yii2\shopify\models\Product;
use davidhirtz\yii2\skeleton\web\Application;
use yii\base\BootstrapInterface;
use yii\base\Event;

/**
 * Class Bootstrap
 * @package davidhirtz\yii2\cms\shopify\bootstrap
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @param Application $app
     */
    public function bootstrap($app)
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

        $app->setMigrationNamespace('davidhirtz\yii2\cms\shopify\migrations');
    }
}