<?php

declare(strict_types=1);

namespace Php\PieUnitTest\Platform\TargetPhp\Exception;

use Php\Pie\Platform\TargetPhp\Exception\ExtensionPathProblem;
use Php\Pie\Platform\TargetPhp\PhpBinaryPath;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ExtensionPathProblem::class)]
final class ExtensionPathProblemTest extends TestCase
{
    public function testExtensionPathNotSet(): void
    {
        $php = PhpBinaryPath::fromCurrentProcess();

        self::assertSame(
            'Could not determine extension path for ' . $php->phpBinaryPath . '; extension_dir => not set',
            ExtensionPathProblem::new($php, null)->getMessage(),
        );
    }

    public function testExtensionPathDoesNotExist(): void
    {
        $php = PhpBinaryPath::fromCurrentProcess();

        self::assertSame(
            'Could not determine extension path for ' . $php->phpBinaryPath . '; extension_dir => /path/does/not/exist; does not exist',
            ExtensionPathProblem::new($php, '/path/does/not/exist')->getMessage(),
        );
    }

    public function testExtensionPathIsAFile(): void
    {
        $php = PhpBinaryPath::fromCurrentProcess();

        self::assertSame(
            'Could not determine extension path for ' . $php->phpBinaryPath . '; extension_dir => ' . __FILE__ . '; exists, not a directory',
            ExtensionPathProblem::new($php, __FILE__)->getMessage(),
        );
    }

    public function testExtensionPathIsADir(): void
    {
        $php = PhpBinaryPath::fromCurrentProcess();

        self::assertSame(
            'Could not determine extension path for ' . $php->phpBinaryPath . '; extension_dir => ' . __DIR__ . '; exists, is a directory',
            ExtensionPathProblem::new($php, __DIR__)->getMessage(),
        );
    }
}
