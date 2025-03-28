<?php

declare(strict_types=1);

use davidhirtz\yii2\cms\shopify\Bootstrap;
use yii\web\Session;

if (is_file(__DIR__ . '/db.php')) {
    require(__DIR__ . '/db.php');
}

return [
    'aliases' => [
        // This is a fix for the broken aliasing of `BaseMigrateController::getNamespacePath()`
        '@davidhirtz/yii2/cms/shopify' => __DIR__ . '/../../src/',
    ],
    'bootstrap' => [
        Bootstrap::class,
    ],
    'components' => [
        'db' => [
            'dsn' => getenv('MYSQL_DSN') ?: 'mysql:host=127.0.0.1;dbname=yii2_cms_shopify_test',
            'username' => getenv('MYSQL_USER') ?: 'root',
            'password' => getenv('MYSQL_PASSWORD') ?: '',
            'charset' => 'utf8',
        ],
        'session' => [
            'class' => Session::class,
        ],
    ],
    'params' => [
        'cookieValidationKey' => 'test',
    ],
];
