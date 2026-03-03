<?php

declare(strict_types=1);

namespace Php\PieUnitTest\DependencyResolver\DependencyInstaller;

use Php\Pie\DependencyResolver\DependencyInstaller\PrescanSystemDependencies;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PrescanSystemDependencies::class)]
final class PrescanSystemDependenciesTest extends TestCase
{
    public function testNoPackageManager(): void
    {
        self::markTestIncomplete('todo'); // @todo
    }

    public function testAllDependenciesSatisifed(): void
    {
        self::markTestIncomplete('todo'); // @todo
    }

    public function testMissingDependencyThatDoesNotHaveAnyPackageManagerDefinition(): void
    {
        self::markTestIncomplete('todo'); // @todo
    }

    public function testMissingDependencyThatDoesNotHaveMyPackageManagerDefinition(): void
    {
        self::markTestIncomplete('todo'); // @todo
    }

    public function testMissingDependenciesFailToInstall(): void
    {
        self::markTestIncomplete('todo'); // @todo
    }

    public function testMissingDependenciesAreSuccessfullyInstalled(): void
    {
        self::markTestIncomplete('todo'); // @todo
    }
}
