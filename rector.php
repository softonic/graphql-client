<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\Config\RectorConfig;
use Rector\EarlyReturn\Rector\Return_\ReturnBinaryOrToEarlyReturnRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use Rector\TypeDeclaration\Rector\ArrowFunction\AddArrowFunctionReturnTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictTypedCallRector;

return RectorConfig::configure()
    ->withSkip(
        [
            // CodeQuality
            CompleteDynamicPropertiesRector::class,
            DisallowedEmptyRuleFixerRector::class,
            // CodingStyle
            CatchExceptionNameMatchingTypeRector::class,
            EncapsedStringsToSprintfRector::class,
            // EarlyReturn
            ReturnBinaryOrToEarlyReturnRector::class,
            // TypeDeclaration
            AddArrowFunctionReturnTypeRector::class,
            ReturnTypeFromStrictTypedCallRector::class,
        ]
    )
    ->withAutoloadPaths([__DIR__ . '/vendor/autoload.php'])
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withImportNames()
    ->withPhpSets(php83: true)
    ->withSets(
        [
            PHPUnitSetList::PHPUNIT_100,
            PHPUnitSetList::PHPUNIT_110,
        ]
    )
    ->withPreparedSets(
        deadCode:         true,
        codeQuality:      true,
        codingStyle:      true,
        typeDeclarations: true,
        earlyReturn:      true
    );
