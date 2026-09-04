<?php

declare(strict_types=1);

namespace Hirtz\Cms\Shopify\Widgets\Forms\Fields;

use Hirtz\Skeleton\I18n\Lang;
use Hirtz\Cms\Models\Entry;
use Hirtz\Cms\Modules\Admin\Widgets\Forms\EntryActiveForm;
use Hirtz\Shopify\Models\Product;
use Hirtz\Skeleton\Widgets\Forms\Fields\SelectField;
use Yii;

/**
 * @property EntryActiveForm $form
 */
class ProductIdSelectField extends SelectField
{
    #[\Override]
    protected function configure(): void
    {
        $this->property ??= 'product_id';
        $this->label ??= Lang::t('shopify', 'COMMON_PRODUCT');
        $this->items = $this->items ?: $this->getProductIdItems();

        parent::configure();
    }

    protected function getProductIdItems(): array
    {
        $takenProductIds = $this->getTakenProductIds();

        $products = Product::find()
            ->select(['id', 'status', 'name'])
            ->filterWhere(['not in', 'id', $takenProductIds])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        $items = [];

        foreach ($products as $product) {
            $items[$product->id] = !$product->isEnabled()
                ? ('[' . $product->getStatusName() . "] $product->name")
                : $product->name;
        }

        return $items;
    }

    protected function getTakenProductIds(): array
    {
        return Entry::find()
            ->select('product_id')
            ->where(['IS NOT', 'product_id', null])
            ->andFilterWhere(['!=', 'id', $this->form->model->id])
            ->column();
    }
}
