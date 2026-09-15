<?php

declare(strict_types=1);

namespace Hirtz\Cms\Shopify\Widgets\Forms\Fields;

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
    public ?string $property = 'product_id';

    /**
     * An entry does not have to stand for a product, and the empty option is how one is unlinked again.
     */
    protected string|false $prompt = '';

    #[\Override]
    protected function configure(): void
    {
        $this->label ??= Yii::t('shopify', 'COMMON_PRODUCT');
        $this->items = $this->items ?: $this->getProductIdItems();

        parent::configure();
    }

    /**
     * @return array<int, string>
     */
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

    /**
     * @return array<int, mixed>
     */
    protected function getTakenProductIds(): array
    {
        return Entry::find()
            ->select('product_id')
            ->where(['IS NOT', 'product_id', null])
            ->andFilterWhere(['!=', 'id', $this->form->model->id])
            ->column();
    }
}
