<?php

declare(strict_types=1);

use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // get parameters
    $parameters = $containerConfigurator->parameters();

    // Define what rule sets will be applied
    $containerConfigurator->import(SetList::DEAD_CODE);
    $containerConfigurator->import(SetList::PHP_80);
    $containerConfigurator->import(SetList::TYPE_DECLARATION_STRICT);
    $containerConfigurator->import(SetList::TYPE_DECLARATION);
    $containerConfigurator->import(SetList::EARLY_RETURN);
    $containerConfigurator->import(SetList::PRIVATIZATION);

    // get services (needed for register a single rule)
    // $services = $containerConfigurator->services();

    // register a single rule
    // $services->set(TypedPropertyRector::class);
};
