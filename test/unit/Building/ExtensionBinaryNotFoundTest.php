<?php

declare(strict_types=1);

namespace Php\PieUnitTest\Building;

use Php\Pie\Building\ExtensionBinaryNotFound;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ExtensionBinaryNotFound::class)]
final class ExtensionBinaryNotFoundTest extends TestCase
{
    public function testFromPrePackagedBinary(): void
    {
        self::assertSame(
            'Expected pre-packaged binary does not exist: /foo/bar',
            ExtensionBinaryNotFound::fromPrePackagedBinary('/foo/bar')->getMessage(),
        );
    }

    public function testFromExpectedBinary(): void
    {
        self::assertSame(
            'Build complete, but expected /foo/bar does not exist.',
            ExtensionBinaryNotFound::fromExpectedBinary('/foo/bar')->getMessage(),
        );
    }
}
