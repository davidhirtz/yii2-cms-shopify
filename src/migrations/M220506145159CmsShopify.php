<?php

declare(strict_types=1);

namespace davidhirtz\yii2\cms\shopify\migrations;

use davidhirtz\yii2\cms\migrations\traits\I18nTablesTrait;
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
