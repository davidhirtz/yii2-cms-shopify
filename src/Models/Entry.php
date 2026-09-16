<?php

declare(strict_types=1);

namespace Hirtz\Cms\Shopify\Models;

use Hirtz\Shopify\Models\Traits\ProductRelationTrait;

/**
 * @property int|null $product_id
 */
class Entry extends \Hirtz\Cms\Models\Entry
{
    use ProductRelationTrait;
}
