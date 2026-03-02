<?php

declare(strict_types=1);

namespace Php\PieUnitTest\DependencyResolver;

use Php\Pie\DependencyResolver\RequestedPackageAndVersion;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RequestedPackageAndVersion::class)]
final class RequestedPackageAndVersionTest extends TestCase
{
    public function testPrettyNameAndVersionWithVersion(): void
    {
        self::assertSame(
            'foo/foo:^1.2.3',
            (new RequestedPackageAndVersion('foo/foo', '^1.2.3'))->prettyNameAndVersion(),
        );
    }

    public function testPrettyNameAndVersionWithoutVersion(): void
    {
        self::assertSame(
            'foo/foo',
            (new RequestedPackageAndVersion('foo/foo', null))->prettyNameAndVersion(),
        );
    }
}
