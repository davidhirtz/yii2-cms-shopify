<?php

declare(strict_types=1);

namespace Hirtz\Cms\Shopify\Tests\Events;

use Hirtz\Cms\Models\Builders\EntrySiteRelationsBuilder;
use Hirtz\Cms\Shopify\Events\ProductEntrySiteRelationsBuilderEventHandler;
use Hirtz\Cms\Shopify\Models\Entry;
use Hirtz\Cms\Shopify\Test\TestCase;
use Hirtz\Cms\Shopify\Test\Traits\CmsShopifyFixtureTrait;
use Hirtz\Shopify\Models\Product;
use Yii;

class ProductEntrySiteRelationsBuilderEventHandlerTest extends TestCase
{
    use CmsShopifyFixtureTrait;

    public function testProductsWithoutVariants(): void
    {
        $builder = $this->getEntrySiteRelationsBuilder();

        self::assertArrayHasKey('product', $builder->entry->getRelatedRecords());

        /** @var Product $product */
        $product = $builder->entry->getRelatedRecords()['product'];

        self::assertFalse($product->isRelationPopulated('variants'));
        self::assertEquals(1, $product->getRelatedRecords()['variant']->id);
    }

    public function testProductsWithVariants(): void
    {
        Yii::$container->set(ProductEntrySiteRelationsBuilderEventHandler::class, [
            'autoloadVariants' => true,
        ]);

        $builder = $this->getEntrySiteRelationsBuilder();

        self::assertArrayHasKey('product', $builder->entry->getRelatedRecords());

        /** @var Product $product */
        $product = $builder->entry->getRelatedRecords()['product'];

        self::assertTrue($product->isRelationPopulated('variants'));
        self::assertEquals(1, $product->getRelatedRecords()['variant']->id);
    }

    private function getEntrySiteRelationsBuilder(): EntrySiteRelationsBuilder
    {
        $entry = Entry::findOne(1);
        $data = $this->getProductFixtureData('product-1');
        $entry->product_id = $data['id'];

        return new EntrySiteRelationsBuilder([
            'entry' => $entry,
        ]);
    }
}
