<?php

declare(strict_types=1);

namespace Hirtz\Cms\Shopify\Migrations;

use Hirtz\Cms\Migrations\Traits\I18nTablesTrait;
use Hirtz\Cms\Models\Entry;
use Hirtz\Shopify\Models\Product;
use Hirtz\Skeleton\Db\Traits\MigrationTrait;
use yii\db\Migration;

/**
 * @noinspection PhpUnused
 */

class M220506145159CmsShopify extends Migration
{
    use MigrationTrait;
    use I18nTablesTrait;

    public function safeUp(): void
    {
        $this->i18nTablesCallback(function (): void {
            $this->addColumn(Entry::tableName(), 'product_id', (string)$this->bigInteger()
                ->unsigned()
                ->null()
                ->after('type'));

            $this->createIndex('product_id', Entry::tableName(), 'product_id', true);

            $this->addForeignKey(
                $this->getForeignKeyName(Entry::tableName(), 'product_id_ibfk'),
                Entry::tableName(),
                'product_id',
                Product::tableName(),
                'id',
                'SET NULL'
            );
        });
    }

    public function safeDown(): void
    {
        $this->i18nTablesCallback(function (): void {
            $this->dropColumn(Entry::tableName(), 'product_id');
        });
    }
}
