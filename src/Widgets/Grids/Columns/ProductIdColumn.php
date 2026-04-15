<?php

declare(strict_types=1);

namespace Hirtz\Cms\Shopify\Widgets\Grids\Columns;

use Hirtz\Cms\Models\Entry;
use Hirtz\Cms\Modules\Admin\Widgets\Grids\EntryGridView;
use Hirtz\Shopify\Models\Product;
use Hirtz\Skeleton\Widgets\Grids\Columns\Column;
use Hirtz\Skeleton\Widgets\Icon;
use Hirtz\Skeleton\Widgets\Link;
use Stringable;
use Yii;

/**
 * @property EntryGridView $grid
 */
class ProductIdColumn extends Column
{
    private static ?array $_products = null;

    public function __construct(private readonly string $property = 'product_id')
    {
        $this->content = $this->getContent(...);
        $this->visible = count($this->getProducts()) > 0;

        parent::__construct();
    }

    protected function getContent(Entry $entry): ?Stringable
    {
        $product = $this->getProducts()[$entry->getAttribute($this->property)] ?? null;

        if (!$product) {
            return null;
        }

        $link = Link::make()
            ->text($product->name)
            ->href($product->getShopifyAdminUrl());

        if ($product->status === $entry->status) {
            if ($product->slug !== $entry->getI18nAttribute('slug')) {
                $link->icon(Icon::make()
                    ->name('exclamation-triangle')
                    ->tooltip(Yii::t('yii', '{attribute} must be equal to "{compareValueOrAttribute}".', [
                        'attribute' => $entry->getAttributeLabel('slug'),
                        'compareValueOrAttribute' => $product->slug,
                    ])));
            }

            return $link;
        }

        return $link->icon(Icon::make()
            ->name($product->getStatusIcon())
            ->tooltip($product->getStatusName()));
    }

    /**
     * @return Product[]
     */
    protected function getProducts(): array
    {
        return static::$_products ??= ($productIds = $this->getProductIds())
            ? Product::find()
                ->select(['id', 'status', 'name', 'slug'])
                ->andWhere(['id' => $productIds])
                ->indexBy('id')
                ->all()
            : [];
    }

    protected function getProductIds(): array
    {
        $productIds = [];

        foreach ($this->grid->provider->getModels() as $model) {
            if ($productId = $model->getAttribute($this->property)) {
                $productIds[] = $productId;
            }
        }

        return array_unique($productIds);
    }
}
