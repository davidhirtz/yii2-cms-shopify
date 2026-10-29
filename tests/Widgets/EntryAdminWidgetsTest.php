<?php

declare(strict_types=1);

namespace Hirtz\Cms\Shopify\Tests\Widgets;

use Hirtz\Cms\Models\Entry as CmsEntry;
use Hirtz\Cms\Shopify\Models\Entry;
use Hirtz\Cms\Shopify\Test\TestCase;
use Hirtz\Cms\Shopify\Test\Traits\CmsShopifyFixtureTrait;
use Hirtz\Cms\Modules\Admin\Widgets\Forms\EntryActiveForm;
use Hirtz\Cms\Modules\Admin\Widgets\Grids\EntryGridView;
use Hirtz\Cms\Shopify\Widgets\Grids\Columns\ProductIdColumn;
use Hirtz\Shopify\Models\Product;
use Hirtz\Skeleton\Widgets\Forms\Fields\Field;
use Hirtz\Skeleton\Widgets\Grids\Columns\DataColumn;
use Hirtz\Skeleton\Widgets\Widget;
use yii\base\Event;
use Hirtz\Skeleton\Models\User;
use Hirtz\Skeleton\Test\Fixtures\UserFixture;
use Override;
use Yii;

/**
 * The bundle adds the product field and column through `EVENT_CONFIGURE`, so no project subclasses the entry widgets.
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

    /**
     * Read off the form itself, since a later listener is handed the rows the bundle's own already contributed to.
     */
    public function testTheFieldFollowsTheNameField(): void
    {
        $this->login();

        $properties = [];

        Event::on(
            EntryActiveForm::class,
            Widget::EVENT_CONFIGURE,
            static function (Event $event) use (&$properties): void {
                self::assertInstanceOf(EntryActiveForm::class, $event->sender);

                $event->sender->rows(static function (array $fieldsets) use (&$properties): array {
                    foreach ($fieldsets as $fieldset) {
                        foreach ($fieldset->getRows() as $field) {
                            $properties[] = match (true) {
                                $field instanceof Field && (bool)$field->getProperty() => $field->getProperty(),
                                is_string($field) => $field,
                                default => $field::class,
                            };
                        }
                    }

                    return $fieldsets;
                });
            }
        );

        Yii::$app->runAction('admin/cms/entry/update', ['id' => 1]);

        $name = array_search('name', $properties, true);
        $product = array_search('product_id', $properties, true);

        self::assertIsInt($name, implode(', ', $properties));
        self::assertSame($name + 1, $product, implode(', ', $properties));
    }

    /**
     * An entry does not have to stand for a product, and the empty option is how one is unlinked again.
     */
    public function testTheFieldOffersAnEmptyOption(): void
    {
        $this->login();

        $html = Yii::$app->runAction('admin/cms/entry/update', ['id' => 1]);

        self::assertIsString($html);

        $select = $this->getProductSelect($html);

        self::assertStringContainsString('<option value=""></option>', $select);
        self::assertStringNotContainsString('disabled', $select);
    }

    public function testTheProductIsUnlinkedThroughTheEmptyOption(): void
    {
        $this->login();

        $product = $this->getProductFromFixture('product-1');
        $entry = Entry::findOne(1);
        $entry->product_id = $product->id;

        self::assertSame(1, $entry->update(), print_r($entry->getErrors(), true));

        $this->submit($entry, '');

        self::assertNull(Entry::findOne(1)->product_id);
    }

    public function testTheProductIsSavedThroughTheForm(): void
    {
        $this->login();

        $product = $this->getProductFromFixture('product-1');
        $entry = Entry::findOne(1);

        $_SERVER['REQUEST_METHOD'] = 'POST';

        $request = $this->getWebRequest();
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
     * A column's products are a memo of its own grid's rows, since a static showed a second grid the first one's.
     */
    public function testASecondGridLoadsItsOwnProducts(): void
    {
        $this->login();

        // the first grid holds no product at all, and used to be the set the second one reported
        Yii::$app->runAction('admin/cms/entry/index');

        $product = $this->getProductFromFixture('product-1');

        $entry = Entry::findOne(1);
        $entry->product_id = $product->id;

        self::assertSame(1, $entry->update(), print_r($entry->getErrors(), true));

        $html = Yii::$app->runAction('admin/cms/entry/index');

        self::assertIsString($html);
        self::assertStringContainsString($product->name, $html);
    }

    /**
     * Read off the grid rather than the markup, since a later listener is handed the columns already contributed.
     */
    public function testTheColumnFollowsTheNameColumn(): void
    {
        $this->login();

        $entry = Entry::findOne(1);
        $entry->product_id = $this->getProductFromFixture('product-1')->id;

        self::assertSame(1, $entry->update(), print_r($entry->getErrors(), true));

        $columns = [];

        Event::on(
            EntryGridView::class,
            Widget::EVENT_CONFIGURE,
            static function (Event $event) use (&$columns): void {
                self::assertInstanceOf(EntryGridView::class, $event->sender);

                $event->sender->columns(static function (array $current) use (&$columns): array {
                    $columns = array_map(
                        static fn (mixed $column): string => $column instanceof DataColumn
                            ? (string)$column->getProperty()
                            : (is_object($column) ? $column::class : (string)$column),
                        $current
                    );

                    return $current;
                });
            }
        );

        Yii::$app->runAction('admin/cms/entry/index');

        $name = array_search('name', $columns, true);
        $product = array_search(ProductIdColumn::class, $columns, true);

        self::assertIsInt($name, implode(', ', $columns));
        self::assertSame($name + 1, $product, implode(', ', $columns));
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

    private function getProductSelect(string $html): string
    {
        $start = strpos($html, 'name="Entry[product_id]"');
        self::assertIsInt($start);

        $start = strrpos(substr($html, 0, $start), '<select');
        self::assertIsInt($start);

        $end = strpos($html, '</select>', $start);
        self::assertIsInt($end);

        return substr($html, $start, $end - $start);
    }

    private function submit(Entry $entry, string $productId): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $request = $this->getWebRequest();
        $request->setBodyParams([
            'Entry' => [
                'status' => $entry->status,
                'type' => $entry->type,
                'name' => $entry->name,
                'slug' => $entry->slug,
                'product_id' => $productId,
            ],
            $request->csrfParam => $request->getCsrfToken(),
        ]);

        Yii::$app->runAction('admin/cms/entry/update', ['id' => $entry->id]);
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

        $this->getWebUser()->setIdentity($user);

        return $user;
    }
}
