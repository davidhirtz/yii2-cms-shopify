<?php

declare(strict_types=1);

use Hirtz\Cms\Shopify\Bootstrap;

$basePath = (getenv('BASE_PATH') ?: getcwd());
$config = require("$basePath/vendor/davidhirtz/yii2-shopify/config/test.php");

return [
    ...$config,
//    'bootstrap' => [
//        Bootstrap::class,
//    ],
];
