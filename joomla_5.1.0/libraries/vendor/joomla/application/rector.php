<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Php71\Rector\ClassConst\PublicConstantVisibilityRector;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/Tests',
    ]);

    $rectorConfig->importNames(true);
    $rectorConfig->importShortClasses(false);

    $rectorConfig->rule(PublicConstantVisibilityRector::class);
};
