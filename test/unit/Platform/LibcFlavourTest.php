<?php

declare(strict_types=1);

namespace Php\PieUnitTest\Platform;

use Php\Pie\Platform\LibcFlavour;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LibcFlavour::class)]
final class LibcFlavourTest extends TestCase
{
    public function testGlibcFlavourIsDetected(): void
    {
        self::fail('todo'); // @todo 436
    }

    public function testMuslFlavourIsDetected(): void
    {
        self::fail('todo'); // @todo 436
    }
}
