<?php

declare(strict_types=1);

namespace Hirtz\Cms\Shopify\Tests\Widgets;

use Hirtz\Cms\Models\Entry as CmsEntry;
use Hirtz\Cms\Shopify\Models\Entry;
use Hirtz\Cms\Shopify\Test\TestCase;
use Hirtz\Cms\Shopify\Test\Traits\CmsShopifyFixtureTrait;
use Hirtz\Shopify\Models\Product;
use Hirtz\Shopify\Test\Fixtures\ProductFixture;
use Hirtz\Shopify\Test\Fixtures\ProductImageFixture;
use Hirtz\Shopify\Test\Fixtures\ProductVariantFixture;
use Hirtz\Skeleton\Models\User;
use Hirtz\Skeleton\Test\Fixtures\UserFixture;
use Override;
use Yii;

/**
 * The bundle adds the product field and column to the cms entry admin itself, through `EVENT_CONFIGURE` — a
 * project should not have to subclass `EntryActiveForm` and `EntryGridView` to link an entry to a product.
 */
class EntryAdminWidgetsTest extends TestCase
{
    use CmsShopifyFixtureTrait {
        CmsShopifyFixtureTrait::fixtures as cmsShopifyFixtures;
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function fixtures(): array
    {
        return [
            ...$this->cmsShopifyFixtures(),
            'user' => UserFixture::class,
        ];
    }

    public function testTheEntryFormOffersTheProducts(): void
    {
        $this->login();

        $html = Yii::$app->runAction('admin/cms/entry/update', ['id' => 1]);

        self::assertIsString($html);
        self::assertStringContainsString('name="Entry[product_id]"', $html);
        self::assertStringContainsString('Sample T-Shirt', $html);
    }

    /**
     * An entry stands for one product, so a product another entry already holds is not offered again.
     */
    public function testAProductAnotherEntryHoldsIsNotOffered(): void
    {
        $this->login();

        $product = $this->getProductFromFixture('product-1');

        $other = Entry::findOne(2);
        $other->product_id = $product->id;

        self::assertSame(1, $other->update(), print_r($other->getErrors(), true));

        $html = Yii::$app->runAction('admin/cms/entry/update', ['id' => 1]);

        self::assertIsString($html);
        self::assertStringNotContainsString(">$product->name<", $html);

        // The other product is still on offer, so the field really rendered.
        self::assertStringContainsString('>' . $this->getProductFromFixture('product-2')->name . '<', $html);
    }

    public function testTheProductIsSavedThroughTheForm(): void
    {
        $this->login();

        $product = $this->getProductFromFixture('product-1');
        $entry = Entry::findOne(1);

        $_SERVER['REQUEST_METHOD'] = 'POST';

        $request = Yii::$app->getRequest();
        $request->setBodyParams([
            'Entry' => [
                'status' => $entry->status,
                'type' => $entry->type,
                'name' => $entry->name,
                'slug' => $entry->slug,
                'product_id' => (string)$product->id,
            ],
            $request->csrfParam => $request->getCsrfToken(),
        ]);

        Yii::$app->runAction('admin/cms/entry/update', ['id' => 1]);

        self::assertSame($product->id, Entry::findOne(1)->product_id);
    }

    public function testTheEntryIndexShowsTheProductOfAnEntry(): void
    {
        $this->login();

        $product = $this->getProductFromFixture('product-1');

        $entry = Entry::findOne(1);
        $entry->product_id = $product->id;

        self::assertSame(1, $entry->update(), print_r($entry->getErrors(), true));

        $html = Yii::$app->runAction('admin/cms/entry/index');

        self::assertIsString($html);
        self::assertStringContainsString($product->name, $html);
    }

    /**
     * The column is only worth a table cell once an entry on the page actually points at a product.
     */
    public function testTheColumnIsHiddenWhileNoEntryHasAProduct(): void
    {
        $this->login();

        $entry = Entry::findOne(1);
        self::assertNull($entry->product_id);

        $html = Yii::$app->runAction('admin/cms/entry/index');

        self::assertIsString($html);

        // The grid rendered the entry, and no product cell with it.
        self::assertStringContainsString($entry->name, $html);
        self::assertStringNotContainsString($this->getProductFromFixture('product-1')->name, $html);
    }

    private function login(): User
    {
        /** @var UserFixture $fixture */
        $fixture = $this->getFixture('user');
        $user = User::findOne($fixture->data['admin']['id']);

        $auth = Yii::$app->getAuthManager();

        foreach ([CmsEntry::AUTH_ENTRY, Product::AUTH_SHOPIFY_PRODUCT] as $permission) {
            $auth->assign($auth->getPermission($permission), $user->id);
        }

        Yii::$app->getUser()->setIdentity($user);

        return $user;
    }
}
