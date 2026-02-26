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
    public function testAsPrettyStringWhenNotInstalled(): void
    {
        self::assertSame(
            'foo: > 2.0.0 ' . Emoji::PROHIBITED . ' (not installed)',
            (new DependencyStatus('foo', new Constraint('>', '2.0.0'), null))->asPrettyString(),
        );
    }

    public function testAsPrettyStringWhenInstalledAndMatchesAllConstraint(): void
    {
        self::assertSame(
            'foo: * ' . Emoji::GREEN_CHECKMARK,
            (new DependencyStatus('foo', new MatchAllConstraint(), new Constraint('=', '1.0.0.0')))->asPrettyString(),
        );
    }

    public function testAsPrettyStringWhenInstalledAndMatchesSemverConstraint(): void
    {
        self::assertSame(
            'foo: ^1.0 ' . Emoji::GREEN_CHECKMARK,
            (new DependencyStatus('foo', (new VersionParser())->parseConstraints('^1.0'), new Constraint('=', '1.0.0.0')))->asPrettyString(),
        );
    }

    public function testAsPrettyStringWhenInstalledButMismatchingVersion(): void
    {
        self::assertSame(
            'foo: > 2.0.0 ' . Emoji::PROHIBITED . ' (your version is 1.2.3.0)',
            (new DependencyStatus('foo', new Constraint('>', '2.0.0'), new Constraint('=', '1.2.3.0')))->asPrettyString(),
        );
    }
}
