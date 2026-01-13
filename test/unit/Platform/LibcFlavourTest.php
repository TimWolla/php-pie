<?php

declare(strict_types=1);

namespace Php\PieUnitTest\Platform;

use Php\Pie\Platform\LibcFlavour;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RequiresOperatingSystemFamily;
use PHPUnit\Framework\TestCase;

use function getenv;
use function putenv;
use function realpath;

use const PATH_SEPARATOR;

#[CoversClass(LibcFlavour::class)]
final class LibcFlavourTest extends TestCase
{
    private const GLIBC_PATH = __DIR__ . '/../../assets/fake-ldd/glibc';
    private const MUSL_PATH  = __DIR__ . '/../../assets/fake-ldd/musl';

    #[RequiresOperatingSystemFamily('Linux')]
    public function testGlibcFlavourIsDetected(): void
    {
        $oldPath = getenv('PATH');
        putenv('PATH=' . realpath(self::GLIBC_PATH) . PATH_SEPARATOR . $oldPath);

        self::assertSame(LibcFlavour::Gnu, LibcFlavour::detect());

        putenv('PATH=' . $oldPath);
    }

    #[RequiresOperatingSystemFamily('Linux')]
    public function testMuslFlavourIsDetected(): void
    {
        $oldPath = getenv('PATH');
        putenv('PATH=' . realpath(self::MUSL_PATH) . PATH_SEPARATOR . $oldPath);

        self::assertSame(LibcFlavour::Musl, LibcFlavour::detect());

        putenv('PATH=' . $oldPath);
    }
}
