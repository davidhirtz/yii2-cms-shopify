<?php

declare(strict_types=1);

namespace davidhirtz\yii2\cms\shopify\models;

use davidhirtz\yii2\shopify\models\traits\ProductRelationTrait;

class Entry extends \davidhirtz\yii2\cms\models\Entry
{
    use ProductRelationTrait;
}
