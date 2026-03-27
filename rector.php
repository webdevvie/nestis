<?php
declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Symfony\Symfony61\Rector\Class_\CommandPropertyToAttributeRector;

return RectorConfig::configure()
                   ->withPaths([
                                   __DIR__ . '/src',
                                   __DIR__ . '/Tests',
                               ])
                   ->withComposerBased(
                       twig    : true,
                       doctrine: true,
                       phpunit : true,
                       symfony : true
                   )
                   ->withImportNames(
                       removeUnusedImports: true
                   )
                   ->withAttributesSets(
                       symfony : true,
                       doctrine: true,
                       phpunit : true,
                       jms     : true
                   );

