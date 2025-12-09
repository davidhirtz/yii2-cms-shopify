<?php

declare(strict_types=1);

namespace Hirtz\Cms\shopify;

use Hirtz\Cms\Models\Entry;
use Hirtz\Cms\Modules\Admin\Widgets\Forms\EntryActiveForm;
use Hirtz\Cms\shopify\behaviors\EntryProductBehavior;
use Hirtz\Cms\shopify\behaviors\ProductEntryBehavior;
use Hirtz\Cms\shopify\widgets\forms\ProductIdFieldBehavior;
use Hirtz\Shopify\models\Product;
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

        Event::on(EntryActiveForm::class, BaseActiveRecord::EVENT_INIT, function (Event $event): void {
            /** @var EntryActiveForm $form */
            $form = $event->sender;
            $form->attachBehavior('ProductIdFieldBehavior', ProductIdFieldBehavior::class);
        });

        $app->setMigrationNamespace('Hirtz\Cms\shopify\Migrations');
    }
}
