<?php

declare(strict_types=1);

namespace davidhirtz\yii2\cms\shopify\models;

use davidhirtz\yii2\shopify\models\Product;
use davidhirtz\yii2\shopify\models\traits\ProductRelationTrait;

/**
 * @property int|null $product_id
 * @property-read Product|null $product {@see ProductRelationTrait::getProduct()}
 * @method void populateProductRelation(?Product $product) {@see ProductRelationTrait::populateProductRelation()}
 */
class Entry extends \davidhirtz\yii2\cms\models\Entry
{
}
