<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

return RectorConfig::configure()
    ->withPhpSets(php81: true)
    ->withRules([
        DeclareStrictTypesRector::class,
    ])
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);