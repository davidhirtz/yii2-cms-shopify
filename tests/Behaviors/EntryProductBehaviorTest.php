<?php

declare(strict_types=1);

namespace Hirtz\Cms\Shopify\Tests\Behaviors;

use Hirtz\Cms\Models\Entry as BaseEntry;
use Hirtz\Cms\Shopify\Models\Entry;
use Hirtz\Cms\Shopify\Test\TestCase;
use Hirtz\Cms\Shopify\Test\Traits\CmsShopifyFixtureTrait;
use yii\base\Event;
use yii\base\Model;

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

    public function testDeletingTheProductDisablesAnEntryThatNoLongerValidates(): void
    {
        $entry = Entry::findOne(1);
        $product = $this->getProductFromFixture('product-1');

        $entry->populateProductRelation($product);
        self::assertSame(1, $entry->update());

        $handler = function (Event $event): void {
            if ($event->sender instanceof Model) {
                $event->sender->addError('name', 'Invalid');
            }
        };
        Event::on(BaseEntry::class, Model::EVENT_AFTER_VALIDATE, $handler);

        try {
            $product->delete();
        } finally {
            Event::off(BaseEntry::class, Model::EVENT_AFTER_VALIDATE, $handler);
        }

        $entry->refresh();

        self::assertSame(Entry::STATUS_DISABLED, $entry->status);
        self::assertNull($entry->product_id);
    }
}
