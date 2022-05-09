<?php

namespace davidhirtz\yii2\cms\shopify\migrations;

use davidhirtz\yii2\cms\models\Entry;
use davidhirtz\yii2\shopify\models\Product;
use davidhirtz\yii2\skeleton\db\MigrationTrait;
use yii\db\Migration;

/**
 * Class M220506145159CmsShopify
 * @package davidhirtz\yii2\cms\shopify\migrations
 * @noinspection PhpUnused
 */
class M220506145159CmsShopify extends Migration
{
    use MigrationTrait;

    /**
     * @inheritDoc
     */
    public function safeUp()
    {
        $this->addColumn(Entry::tableName(), 'product_id', $this->bigInteger()->unsigned()->null()->after('type'));
        $this->createIndex('product_id', Entry::tableName(), 'product_id', true);

        $schema = $this->getDb()->getSchema();
        $tableName = $schema->getRawTableName(Entry::tableName());
        $this->addForeignKey("{$tableName}_product_id_ibfk", Entry::tableName(), 'product_id', Product::tableName(), 'id', 'SET NULL');
    }

    /**
     * @inheritDoc
     */
    public function safeDown()
    {
        $this->dropColumn(Entry::tableName(), 'product_id');
    }
}