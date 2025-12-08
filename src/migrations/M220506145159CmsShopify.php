<?php

declare(strict_types=1);

namespace Hirtz\Cms\shopify\migrations;

use Hirtz\Cms\migrations\traits\I18nTablesTrait;
use Hirtz\Cms\models\Entry;
use Hirtz\Shopify\models\Product;
use Hirtz\Skeleton\db\traits\MigrationTrait;
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
        $this->i18nTablesCallback(function () {
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
        $this->i18nTablesCallback(function () {
            $this->dropColumn(Entry::tableName(), 'product_id');
        });
    }
}
