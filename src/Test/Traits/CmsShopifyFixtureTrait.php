<?php

declare(strict_types=1);

namespace Hirtz\Cms\Shopify\Test\Traits;

use Hirtz\Cms\Test\Fixtures\Traits\CmsFixtureTrait;
use Hirtz\Shopify\Test\Traits\ShopifyFixtureTrait;

trait CmsShopifyFixtureTrait
{
    use CmsFixtureTrait {
        CmsFixtureTrait::fixtures as cmsFixtures;
    }
    use ShopifyFixtureTrait {
        ShopifyFixtureTrait::fixtures as protected shopifyFixtures;
    }

    /**
     * @return array<string, mixed>
     */
    public function fixtures(): array
    {
        return [
            ...$this->cmsFixtures(),
            ...$this->shopifyFixtures(),
        ];
    }
}
