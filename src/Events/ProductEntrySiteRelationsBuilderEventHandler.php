<?php

declare(strict_types=1);

namespace Hirtz\Cms\Shopify\Events;

use Hirtz\Cms\Models\Events\EntrySiteRelationsBuilderEvent;
use Hirtz\Shopify\Models\Product;
use Yii;

class ProductEntrySiteRelationsBuilderEventHandler
{
    public function __invoke(EntrySiteRelationsBuilderEvent $event): void
    {
        $autoloadVariants = Yii::$container->getDefinitions()[self::class]['autoloadVariants'] ?? false;
        $productIds = [];

        foreach ($event->sender->entries as $entry) {
            $productIds[] = $entry->getAttribute('product_id');
        }

        $productIds = array_filter(array_unique($productIds));

        if ($productIds) {
            Yii::debug('Loading related products ...');

            $products = Product::find()
                ->whereStatus()
                ->andWhere(['id' => $productIds])
                ->with($autoloadVariants ? 'variants' : 'variant')
                ->indexBy('id')
                ->all();
        }

        // An entry names neither by default, and `null` is not a legal array offset.
        foreach ($event->sender->entries as $entry) {
            $productId = $entry->getAttribute('product_id');
            $product = $productId === null ? null : $products[$productId] ?? null;

            $entry->populateRelation('product', $product);

            if ($product?->isRelationPopulated('variants')) {
                // read into a local: `reset()` takes its argument by reference, which a relation cannot answer
                $variants = $product->variants;
                $variantId = $entry->getAttribute('variant_id');
                $variant = $variantId === null ? null : $variants[$variantId] ?? null;

                $product->populateRelation('variant', $variant ?? (reset($variants) ?: null));
            }
        }
    }
}
