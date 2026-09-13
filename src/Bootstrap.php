<?php

declare(strict_types=1);

namespace Hirtz\Cms\Shopify;

use Hirtz\Cms\Models\Builders\EntrySiteRelationsBuilder;
use Hirtz\Cms\Models\Entry;
use Hirtz\Cms\Modules\Admin\Widgets\Forms\EntryActiveForm;
use Hirtz\Cms\Modules\Admin\Widgets\Grids\EntryGridView;
use Hirtz\Cms\Shopify\Behaviors\EntryProductBehavior;
use Hirtz\Cms\Shopify\Behaviors\ProductEntryBehavior;
use Hirtz\Cms\Shopify\Events\ProductEntrySiteRelationsBuilderEventHandler;
use Hirtz\Cms\Shopify\Widgets\Forms\Fields\ProductIdSelectField;
use Hirtz\Cms\Shopify\Widgets\Grids\Columns\ProductIdColumn;
use Hirtz\Shopify\Models\Product;
use Hirtz\Skeleton\Modules\Admin\Controllers\DashboardController;
use Hirtz\Skeleton\Web\Application;
use Hirtz\Skeleton\Widgets\Widget;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\db\BaseActiveRecord;

class Bootstrap implements BootstrapInterface
{
    /**
     * @param Application<\Hirtz\Skeleton\Models\User> $app
     */
    public function bootstrap($app): void
    {
        // The cached products outlive the application that loaded them.
        ProductIdColumn::reset();

        Event::on(Entry::class, BaseActiveRecord::EVENT_INIT, function (Event $event): void {
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

        $this->addEntryAdminWidgets();

        DashboardController::addRoles([
            Product::AUTH_SHOPIFY_PRODUCT,
        ]);

        $app->setMigrationNamespace('Hirtz\Cms\Shopify\Migrations');
    }

    /**
     * The product belongs on the entry's own form and grid, so the bundle adds it rather than leaving every
     * project to subclass both widgets. `EVENT_CONFIGURE` fires after the widget built its defaults and before
     * the caller's `prepare()`, so a project still has the last word.
     */
    protected function addEntryAdminWidgets(): void
    {
        Event::on(
            EntryActiveForm::class,
            Widget::EVENT_CONFIGURE,
            static function (Event $event): void {
                /** @var EntryActiveForm $form */
                $form = $event->sender;
                $form->rows(static fn (array $rows): array => self::addProductIdField($rows));
            }
        );

        Event::on(
            EntryGridView::class,
            Widget::EVENT_CONFIGURE,
            static function (Event $event): void {
                /** @var EntryGridView<Entry> $grid */
                $grid = $event->sender;
                $grid->columns(static fn (array $columns): array => self::addProductIdColumn($columns));
            }
        );
    }

    /**
     * `rows` is either a flat list of fields or a list of groups, and the product belongs beside the entry's own
     * attributes — the first group.
     *
     * @param array<mixed> $rows
     * @return array<mixed>
     */
    private static function addProductIdField(array $rows): array
    {
        $field = ProductIdSelectField::make();
        $first = current($rows);

        if (is_array($first)) {
            $key = key($rows);
            $rows[$key] = [...$first, $field];

            return $rows;
        }

        return [...$rows, $field];
    }

    /**
     * Before the button column, which every grid here keeps last.
     *
     * @param array<mixed> $columns
     * @return array<mixed>
     */
    private static function addProductIdColumn(array $columns): array
    {
        $column = ProductIdColumn::make();
        $last = array_pop($columns);

        return $last === null
            ? [$column]
            : [...$columns, $column, $last];
    }
}
