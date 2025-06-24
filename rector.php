<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;

return RectorConfig::configure()
    // Define paths to be analyzed by Rector
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])

    // For Symfony projects, load the container XML to enable smarter refactoring based on service definitions.
    // Adjust the path according to your Symfony version and environment.
    // Typically, it's located at var/cache/{env}/App_Kernel{Env}DebugContainer.xml
    ->withSymfonyContainerXml(__DIR__.'/var/cache/dev/App_KernelDevDebugContainer.xml')

    // Apply framework-specific rules based on Composer dependencies.
    // This enables automatic refactoring for frameworks like Symfony, Doctrine, etc., including version upgrades.
    ->withComposerBased(
        symfony: true,
        doctrine: true,
        phpunit: true,
        twig: true
    )

    // Apply common code quality improvements and PHP version upgrade sets.
    // withPreparedSets() offers a concise way to enable frequently used rule sets.
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
    )

    // Apply rule sets tailored to the latest PHP version.
    // Choose the appropriate LevelSetList for your project's PHP version (e.g., LevelSetList::UP_TO_PHP_84).
    // withSets() is used to explicitly add specific sets not covered by withPreparedSets().
    ->withSets([
        LevelSetList::UP_TO_PHP_84,
        // SetList::EARLY_RETURN, // Example: Enable early return refactoring
        // SetList::INSTANCEOF,   // Example: Optimize instanceof checks
        // SetList::STRICT_BOOLEANS, // Example: Enforce strict boolean usage
        // ---------------------------------------------
        SymfonySetList::CONFIGS,
        SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES, // Example: Convert Symfony annotations to PHP attributes
        SymfonySetList::SYMFONY_CODE_QUALITY,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
        // SymfonySetList::SYMFONY_73, // Example: Specific Symfony 7.3 upgrade rules if needed
        // ---------------------------------------------
        DoctrineSetList::DOCTRINE_CODE_QUALITY,
        // ---------------------------------------------
        PHPUnitSetList::PHPUNIT_120, // Example: PHPUnit 12.0 upgrade rules
    ])

    // Convert annotations to native PHP attributes (for PHP 8.0+).
    // This is useful for migrating from Doctrine or Symfony annotations.
    ->withAttributesSets(
        doctrine: true,
        symfony: true,
    )

    // Add individual Rector rules as needed
    // ->withRules([
    //     YourCustomRectorRule::class,
    // ])

    // Optimize import statements (sort and remove unused 'use' statements)
    ->withImportNames()

    // Exclude specific files or directories from analysis
    ->withSkip([
        // __DIR__ . '/src/SomeLegacyCode.php',
        // SetList::DEAD_CODE => [
        //     __DIR__ . '/src/SpecificFileWithFalsePositive.php',
        // ],
    ]);
