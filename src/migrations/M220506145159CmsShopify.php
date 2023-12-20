<?php

namespace davidhirtz\yii2\cms\shopify\migrations;

use davidhirtz\yii2\cms\models\Entry;
use davidhirtz\yii2\shopify\models\Product;
use davidhirtz\yii2\skeleton\db\traits\MigrationTrait;
use yii\db\Migration;

/**
 * @noinspection PhpUnused
 */

class M220506145159CmsShopify extends Migration
{
    use MigrationTrait;

    public function safeUp(): void
    {
        $this->addColumn(Entry::tableName(), 'product_id', $this->bigInteger()
            ->unsigned()
            ->null()
            ->after('type'));

        $this->createIndex('product_id', Entry::tableName(), 'product_id', true);

        $tableName = $this->getDb()->getSchema()->getRawTableName(Entry::tableName());

        $this->addForeignKey(
            "{$tableName}_product_id_ibfk",
            Entry::tableName(),
            'product_id',
            Product::tableName(),
            'id',
            'SET NULL'
        );
    }

    public function safeDown(): void
    {
        $this->dropColumn(Entry::tableName(), 'product_id');
    }
}
