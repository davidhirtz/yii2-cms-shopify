<?php

declare(strict_types=1);

namespace Hirtz\Cms\Shopify\Tests\Validators;

use Hirtz\Cms\Shopify\Models\Entry;
use Hirtz\Cms\Shopify\Test\TestCase;
use Hirtz\Cms\Shopify\Test\Traits\CmsShopifyFixtureTrait;

/**
 * An entry stands for exactly one Shopify product, so the link has to exist and cannot be shared.
 */
class ProductIdValidatorTest extends TestCase
{
    use CmsShopifyFixtureTrait;

    public function testAProductThatExistsIsAccepted(): void
    {
        $entry = Entry::findOne(1);
        $entry->product_id = $this->getProductFromFixture('product-1')->id;

        self::assertTrue($entry->validate(), print_r($entry->getErrors(), true));
    }

    public function testAProductThatIsNotThereIsRefused(): void
    {
        $entry = Entry::findOne(1);
        $entry->product_id = 99999;

        self::assertFalse($entry->validate());
        self::assertArrayHasKey('product_id', $entry->getErrors());
    }

    public function testTwoEntriesCannotShareAProduct(): void
    {
        $product = $this->getProductFromFixture('product-1');

        $first = Entry::findOne(1);
        $first->product_id = $product->id;

        self::assertSame(1, $first->update());

        $second = Entry::findOne(2);
        $second->product_id = $product->id;

        self::assertFalse($second->validate());
        self::assertArrayHasKey('product_id', $second->getErrors());
    }

    /**
     * Saving the entry that already holds the product must not report it as taken by itself.
     */
    public function testTheEntryKeepsItsOwnProduct(): void
    {
        $entry = Entry::findOne(1);
        $entry->product_id = $this->getProductFromFixture('product-1')->id;

        self::assertSame(1, $entry->update());

        $entry = Entry::findOne(1);
        $entry->name = 'Renamed';

        self::assertTrue($entry->validate(), print_r($entry->getErrors(), true));
        self::assertSame(1, $entry->update());
    }

    /**
     * An empty value is stored as null rather than as a zero pointing at no product.
     */
    public function testAnEmptyValueBecomesNull(): void
    {
        $entry = Entry::findOne(1);
        $entry->product_id = 0;

        self::assertTrue($entry->validate(), print_r($entry->getErrors(), true));
        self::assertNull($entry->product_id);

        $entry->product_id = '';

        self::assertTrue($entry->validate());
        self::assertNull($entry->product_id);
    }
}
