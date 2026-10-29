<?php

declare(strict_types=1);

namespace Hirtz\Cms\Shopify;

use Hirtz\Cms\Models\Actions\PreloadEntrySiteRelations;
use Hirtz\Cms\Models\Entry;
use Hirtz\Cms\Modules\Admin\Widgets\Forms\EntryActiveForm;
use Hirtz\Skeleton\Widgets\Forms\Fieldset;
use Hirtz\Cms\Modules\Admin\Widgets\Grids\EntryGridView;
use Hirtz\Cms\Shopify\Behaviors\EntryProductBehavior;
use Hirtz\Cms\Shopify\Behaviors\ProductEntryBehavior;
use Hirtz\Cms\Shopify\Events\ProductEntrySiteRelationsEventHandler;
use Hirtz\Cms\Shopify\Widgets\Forms\Fields\ProductIdSelectField;
use Hirtz\Cms\Shopify\Widgets\Grids\Columns\ProductIdColumn;
use Hirtz\Shopify\Models\Product;
use Hirtz\Skeleton\Helpers\EventHelper;
use Hirtz\Skeleton\Web\Application;
use Hirtz\Skeleton\Widgets\Forms\Fields\Field;
use Hirtz\Skeleton\Widgets\Grids\Columns\DataColumn;
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
        EventHelper::on(
            Entry::class,
            BaseActiveRecord::EVENT_INIT,
            static fn (Entry $entry) => $entry->attachBehavior('EntryProductBehavior', EntryProductBehavior::class)
        );

        EventHelper::on(
            Product::class,
            BaseActiveRecord::EVENT_INIT,
            static fn (Product $product) => $product->attachBehavior('ProductEntryBehavior', ProductEntryBehavior::class)
        );

        Event::on(
            PreloadEntrySiteRelations::class,
            PreloadEntrySiteRelations::EVENT_AFTER_LOAD_ENTRIES,
            new ProductEntrySiteRelationsEventHandler()
        );

        $this->addEntryAdminWidgets();

        $app->setMigrationNamespace('Hirtz\Cms\Shopify\Migrations');
    }

    /**
     * `EVENT_CONFIGURE` fires after the widget's defaults and before the caller's `prepare()`, so a project still has
     * the last word.
     */
    protected function addEntryAdminWidgets(): void
    {
        EventHelper::on(
            EntryActiveForm::class,
            Widget::EVENT_CONFIGURE,
            static fn (EntryActiveForm $form) => $form->rows(
                static fn (array $fieldsets): array => self::addProductIdField($fieldsets)
            )
        );

        EventHelper::on(
            EntryGridView::class,
            Widget::EVENT_CONFIGURE,
            static fn (EntryGridView $grid) => $grid->columns(
                static fn (array $columns): array => self::addProductIdColumn($columns)
            )
        );
    }

    /**
     * The fieldset holding the name field, or the first one, since a project may have replaced the name field.
     *
     * @param list<Fieldset> $fieldsets
     * @return list<Fieldset>
     */
    private static function addProductIdField(array $fieldsets): array
    {
        $field = ProductIdSelectField::make();

        foreach ($fieldsets as $fieldset) {
            if (self::indexOfName($fieldset->getRows()) !== null) {
                $fieldset->rows(static fn (array $rows): array => self::insertAfterName($rows, $field));
                return $fieldsets;
            }
        }

        if ($fieldsets) {
            $fieldsets[0]->rows(static fn (array $rows): array => [...$rows, $field]);
        }

        return $fieldsets;
    }

    /**
     * @param array<mixed> $columns
     * @return array<mixed>
     */
    private static function addProductIdColumn(array $columns): array
    {
        return self::insertAfterName($columns, ProductIdColumn::make());
    }

    /**
     * Only users of `Widgets\Traits\PropertyTrait` are asked, since another column may declare a private `property`.
     *
     * @param array<mixed> $items
     * @return array<mixed>
     */
    private static function insertAfterName(array $items, object $item): array
    {
        $items = array_values($items);
        $index = self::indexOfName($items);

        if ($index === null) {
            return [...$items, $item];
        }

        array_splice($items, $index + 1, 0, [$item]);

        return $items;
    }

    /**
     * @param array<mixed> $items
     */
    private static function indexOfName(array $items): ?int
    {
        foreach (array_values($items) as $index => $existing) {
            if (($existing instanceof DataColumn || $existing instanceof Field) && $existing->getProperty() === 'name') {
                return $index;
            }
        }

        return null;
    }
}
