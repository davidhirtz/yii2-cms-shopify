<?php

declare(strict_types=1);

namespace Hirtz\Cms\shopify\Models;

use Hirtz\Shopify\Models\Product;
use Hirtz\Shopify\Models\Traits\ProductRelationTrait;

/**
 * @property int|null $product_id
 * @property-read Product|null $product {@see ProductRelationTrait::getProduct()}
 * @method void populateProductRelation(?Product $product) {@see ProductRelationTrait::populateProductRelation()}
 */
class Entry extends \Hirtz\Cms\Models\Entry
{
}
