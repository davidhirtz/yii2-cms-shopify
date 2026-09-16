<?php

declare(strict_types=1);

namespace Hirtz\Cms\Shopify\Tests\Events;

use Hirtz\Cms\Models\Actions\PreloadEntrySiteRelations;
use Hirtz\Cms\Shopify\Events\ProductEntrySiteRelationsEventHandler;
use Hirtz\Cms\Shopify\Models\Entry;
use Hirtz\Cms\Shopify\Test\TestCase;
use Hirtz\Cms\Shopify\Test\Traits\CmsShopifyFixtureTrait;
use Hirtz\Shopify\Models\Product;
use Yii;

class ProductEntrySiteRelationsEventHandlerTest extends TestCase
{
    use CmsShopifyFixtureTrait;

    public function testProductsWithoutVariants(): void
    {
        $preload = $this->createPreload();

        self::assertArrayHasKey('product', $preload->entry->getRelatedRecords());

        /** @var Product $product */
        $product = $preload->entry->getRelatedRecords()['product'];

        self::assertFalse($product->isRelationPopulated('variants'));
        self::assertEquals(1, $product->getRelatedRecords()['variant']->id);
    }

    public function testProductsWithVariants(): void
    {
        Yii::$container->set(ProductEntrySiteRelationsEventHandler::class, [
            'autoloadVariants' => true,
        ]);

        $preload = $this->createPreload();

        self::assertArrayHasKey('product', $preload->entry->getRelatedRecords());

        /** @var Product $product */
        $product = $preload->entry->getRelatedRecords()['product'];

        self::assertTrue($product->isRelationPopulated('variants'));
        self::assertEquals(1, $product->getRelatedRecords()['variant']->id);
    }

    /**
     * @return PreloadEntrySiteRelations
     */
    private function createPreload(): PreloadEntrySiteRelations
    {
        $entry = Entry::findOne(1);
        $data = $this->getProductFixtureData('product-1');
        $entry->product_id = $data['id'];

        return new PreloadEntrySiteRelations([
            'entry' => $entry,
        ]);
    }
}
