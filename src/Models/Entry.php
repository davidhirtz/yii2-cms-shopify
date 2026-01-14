<?php

declare(strict_types=1);

namespace Hirtz\Cms\Shopify\Models;

use Hirtz\Shopify\Models\Traits\ProductRelationTrait;

class Entry extends \Hirtz\Cms\Models\Entry
{
    use ProductRelationTrait;
}
