<?php

declare(strict_types=1);

namespace Php\PieUnitTest\DependencyResolver;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Package\CompletePackage;
use Composer\Package\Link;
use Composer\Semver\Constraint\Constraint;
use Php\Pie\DependencyResolver\FetchDependencyStatuses;
use Php\Pie\Platform\TargetPhp\PhpBinaryPath;
use Php\Pie\Platform\TargetPlatform;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FetchDependencyStatuses::class)]
final class FetchDependencyStatusesTest extends TestCase
{
    public function testNoRequiresReturnsEmptyArray(): void
    {
        $package = new CompletePackage('vendor/foo', '1.2.3.0', '1.2.3');

        self::assertEquals([], (new FetchDependencyStatuses())(TargetPlatform::fromPhpBinaryPath(PhpBinaryPath::fromCurrentProcess(), null, null), $this->createMock(Composer::class), $package));
    }

    public function testRequiresReturnsListOfStatuses(): void
    {
        $php = PhpBinaryPath::fromCurrentProcess();

        $package = new CompletePackage('vendor/foo', '1.2.3.0', '1.2.3');
        $package->setRequires([
            'ext-core' => new Link('__root__', 'ext-core', new Constraint('=', $php->version() . '.0')),
            'ext-nonsense_extension' => new Link('__root__', 'ext-nonsense_extension', new Constraint('=', '*')),
            'ext-standard' => new Link('__root__', 'ext-standard', new Constraint('<', '1.0.0.0')),
        ]);

        $deps = (new FetchDependencyStatuses())(
            TargetPlatform::fromPhpBinaryPath($php, null, null),
            Factory::create($this->createMock(IOInterface::class)),
            $package,
        );

        self::assertCount(3, $deps);

        self::assertSame('ext-core: == ' . $php->version() . '.0 ✅', $deps[0]->asPrettyString());
        self::assertSame('ext-nonsense_extension: == * 🚫 (not installed)', $deps[1]->asPrettyString());
        self::assertSame('ext-standard: < 1.0.0.0 🚫 (your version is ' . $php->version() . '.0)', $deps[2]->asPrettyString());
    }
}
