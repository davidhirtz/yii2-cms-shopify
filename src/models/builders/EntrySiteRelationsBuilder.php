<?php

declare(strict_types=1);

namespace davidhirtz\yii2\cms\shopify\models\builders;

use davidhirtz\yii2\cms\shopify\models\Entry;
use davidhirtz\yii2\shopify\models\Product;
use davidhirtz\yii2\shopify\models\queries\ProductQuery;

/**
 * @extends \davidhirtz\yii2\cms\models\builders\EntrySiteRelationsBuilder<Entry>
 */
class EntrySiteRelationsBuilder extends \davidhirtz\yii2\cms\models\builders\EntrySiteRelationsBuilder
{
    public bool $autoloadVariants = false;

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
            $product = $this->products[$entry->getAttribute('product_id')] ?? null;
            $entry->populateProductRelation($product);

            if ($product->isRelationPopulated('variants')) {
                $variant = $product->variants[$entry->getAttribute('variant_id')]
                    ?? (reset($product->variants) ?: null);

                $product->populateRelation('variant', $variant);
            }
        }
    }

    protected function loadProductVariants(): void
    {
        if (!$this->autoloadVariants) {
            $product = $this->products[$this->entry->getAttribute('product_id')] ?? null;
            $product?->populateRelation('variant', $product->variants[$product->variant_id] ?? null);
        }
    }

    protected function getProductQuery(): ProductQuery
    {
        return Product::find()
            ->whereStatus()
            ->with($this->autoloadVariants ? 'variants' : 'variant');
    }
}
