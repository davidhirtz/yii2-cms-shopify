<?php

declare(strict_types=1);

namespace davidhirtz\yii2\cms\shopify\models;

use davidhirtz\yii2\shopify\models\traits\ProductRelationTrait;
use Yii;

class Entry extends \davidhirtz\yii2\cms\models\Entry
{
    use ProductRelationTrait;

    public function attributeLabels(): array
    {
        return [
            ...parent::attributeLabels(),
            'product_id' => Yii::t('shopify', 'Product'),
        ];
    }
}
