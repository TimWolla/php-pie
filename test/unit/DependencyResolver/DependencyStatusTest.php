<?php

declare(strict_types=1);

namespace Php\PieUnitTest\DependencyResolver;

use Composer\Semver\Constraint\Constraint;
use Composer\Semver\Constraint\MatchAllConstraint;
use Composer\Semver\VersionParser;
use Php\Pie\DependencyResolver\DependencyStatus;
use Php\Pie\Util\Emoji;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DependencyStatus::class)]
final class DependencyStatusTest extends TestCase
{
    public function testDependencyNotInstalled(): void
    {
        $dependencyStatus = new DependencyStatus('foo', new Constraint('>', '2.0.0'), null);
        self::assertSame('foo: > 2.0.0 ' . Emoji::PROHIBITED . ' (not installed)', $dependencyStatus->asPrettyString());
        self::assertFalse($dependencyStatus->satisfied());
    }

    public function testDependencyInstalledAndMatchesAllConstraint(): void
    {
        $dependencyStatus = new DependencyStatus('foo', new MatchAllConstraint(), new Constraint('=', '1.0.0.0'));
        self::assertSame('foo: * ' . Emoji::GREEN_CHECKMARK, $dependencyStatus->asPrettyString());
        self::assertTrue($dependencyStatus->satisfied());
    }

    public function testDependencyInstalledAndMatchesSemverConstraint(): void
    {
        $dependencyStatus = new DependencyStatus('foo', (new VersionParser())->parseConstraints('^1.0'), new Constraint('=', '1.0.0.0'));
        self::assertSame('foo: ^1.0 ' . Emoji::GREEN_CHECKMARK, $dependencyStatus->asPrettyString());
        self::assertTrue($dependencyStatus->satisfied());
    }

    public function testDependencyInstalledButMismatchingVersion(): void
    {
        $dependencyStatus = new DependencyStatus('foo', new Constraint('>', '2.0.0'), new Constraint('=', '1.2.3.0'));
        self::assertSame('foo: > 2.0.0 ' . Emoji::PROHIBITED . ' (your version is 1.2.3.0)', $dependencyStatus->asPrettyString());
        self::assertFalse($dependencyStatus->satisfied());
    }
}
