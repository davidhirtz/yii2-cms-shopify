<?php

declare(strict_types=1);

namespace Hirtz\Cms\Shopify\Tests\Behaviors;

use Hirtz\Cms\Shopify\Models\Entry;
use Hirtz\Cms\Shopify\Test\TestCase;
use Hirtz\Cms\Shopify\Test\Traits\CmsShopifyFixtureTrait;

class EntryProductBehaviorTest extends TestCase
{
    use CmsShopifyFixtureTrait;

    public function testSaveAndDelete(): void
    {
        $entry = Entry::findOne(1);
        $product = $this->getProductFromFixture('product-1');

        $entry->populateProductRelation($product);

        self::assertTrue($entry->update() === 1);
        self::assertEquals($product->id, $entry->product_id);

        $product->delete();
        $entry->refresh();

        self::assertEquals(Entry::STATUS_DISABLED, $entry->status);
        self::assertNull($entry->product_id);
    }
}
