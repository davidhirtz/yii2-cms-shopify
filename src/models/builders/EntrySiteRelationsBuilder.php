<?php

declare(strict_types=1);

namespace davidhirtz\yii2\cms\shopify\models\builders;

use davidhirtz\yii2\shopify\models\Product;
use davidhirtz\yii2\shopify\models\queries\ProductQuery;

class EntrySiteRelationsBuilder extends \davidhirtz\yii2\cms\models\builders\EntrySiteRelationsBuilder
{
    /**
     * @var Product[]
     */
    protected array $products = [];

    protected function loadRelations(): void
    {
        parent::loadRelations();

        $this->loadProducts();
        $this->loadProductVariants();
    }

    protected function loadProducts(): void
    {
        $productIds = [];

        foreach ($this->entries as $entry) {
            $productIds[] = $entry->getAttribute('product_id');
        }

        $productIds = array_filter(array_unique($productIds));

        if ($productIds) {
            $this->products = $this->getProductQuery()
                ->andWhere(['id' => $productIds])
                ->indexBy('id')
                ->all();
        }

        foreach ($this->entries as $entry) {
            $relation = $this->products[$entry->getAttribute('product_id')] ?? null;
            $entry->populateRelation('product', $relation);
        }
    }

    protected function loadProductVariants(): void
    {
        $product = $this->products[$this->entry->getAttribute('product_id')] ?? null;
        $product?->populateRelation('variant', $product->variants[$product->variant_id] ?? null);
    }

    protected function getProductQuery(): ProductQuery
    {
        return Product::find()
            ->whereStatus();
    }
}
