<?php

declare(strict_types=1);

namespace Hirtz\Cms\shopify\models;

use Hirtz\Shopify\models\Product;
use Hirtz\Shopify\models\traits\ProductRelationTrait;

/**
 * @property int|null $product_id
 * @property-read Product|null $product {@see ProductRelationTrait::getProduct()}
 * @method void populateProductRelation(?Product $product) {@see ProductRelationTrait::populateProductRelation()}
 */
class Entry extends \Hirtz\Cms\Models\Entry
{
}
