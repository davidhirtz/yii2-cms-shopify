<?php

declare(strict_types=1);

namespace Hirtz\Cms\Shopify;

use Hirtz\Cms\Models\Builders\EntrySiteRelationsBuilder;
use Hirtz\Cms\Models\Entry;
use Hirtz\Cms\Shopify\Behaviors\EntryProductBehavior;
use Hirtz\Cms\Shopify\Behaviors\ProductEntryBehavior;
use Hirtz\Cms\Shopify\Events\ProductEntrySiteRelationsBuilderEventHandler;
use Hirtz\Shopify\Models\Product;
use Hirtz\Skeleton\Modules\Admin\Controllers\DashboardController;
use Hirtz\Skeleton\Web\Application;
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
        Event::on(Entry::class, Widget::EVENT_INIT, function (Event $event): void {
            /** @var Entry $entry */
            $entry = $event->sender;
            $entry->attachBehavior('EntryProductBehavior', EntryProductBehavior::class);
        });

        Event::on(Product::class, BaseActiveRecord::EVENT_INIT, function (Event $event): void {
            /** @var Product $product */
            $product = $event->sender;
            $product->attachBehavior('ProductEntryBehavior', ProductEntryBehavior::class);
        });

        Event::on(
            EntrySiteRelationsBuilder::class,
            EntrySiteRelationsBuilder::EVENT_AFTER_LOAD_ENTRIES,
            new ProductEntrySiteRelationsBuilderEventHandler()
        );

        DashboardController::addRoles([
            Product::AUTH_PRODUCT_UPDATE,
        ]);

        $app->setMigrationNamespace('Hirtz\Cms\Shopify\Migrations');
    }
}
