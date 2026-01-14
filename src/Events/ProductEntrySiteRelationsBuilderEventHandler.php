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

        foreach ($event->sender->entries as $entry) {
            $product = $products[$entry->getAttribute('product_id')] ?? null;
            $entry->populateRelation('product', $product);

            if ($product?->isRelationPopulated('variants')) {
                $variant = $product->variants[$entry->getAttribute('variant_id')]
                    ?? (reset($product->variants) ?: null);

                $product->populateRelation('variant', $variant);
            }
        }
    }
}
