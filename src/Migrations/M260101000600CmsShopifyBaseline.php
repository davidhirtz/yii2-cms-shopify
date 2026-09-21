<?php

declare(strict_types=1);

namespace Hirtz\Cms\Shopify\Migrations;

use Override;
use yii\db\Migration;

/**
 * @noinspection PhpUnused
 */
class M260101000600CmsShopifyBaseline extends Migration
{
    #[Override]
    public function safeUp(): void
    {
        $this->execute(
            <<<'SQL'
            ALTER TABLE `entry` ADD `product_id` bigint(20) unsigned NULL
            SQL
        );

        $this->execute(
            <<<'SQL'
            ALTER TABLE `entry` ADD UNIQUE KEY `product_id` (`product_id`)
            SQL
        );

        $this->execute(
            <<<'SQL'
            ALTER TABLE `entry` ADD CONSTRAINT `entry_product_id_ibfk` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
            SQL
        );
    }

    #[Override]
    public function safeDown(): bool
    {
        echo "    > a baseline cannot be reverted, restore a dump instead\n";
        return false;
    }
}
