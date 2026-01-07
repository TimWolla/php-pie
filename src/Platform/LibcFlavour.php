<?php

declare(strict_types=1);

namespace Php\Pie\Platform;

use Php\Pie\Util\Process;
use Symfony\Component\Process\ExecutableFinder;
use Throwable;

use function str_contains;

/** @internal This is not public API for PIE, so should not be depended upon unless you accept the risk of BC breaks */
enum LibcFlavour: string
{
    case Gnu  = 'glibc';
    case Musl = 'musl';

    public static function detect(): self
    {
        $executableFinder = new ExecutableFinder();

        $lddPath = $executableFinder->find('ldd');
        $lsPath  = $executableFinder->find('ls');

        if ($lddPath === null || $lsPath === null) {
            return self::Gnu;
        }

        try {
            $linkResult = Process::run([$lddPath, $lsPath]);
        } catch (Throwable) {
            return self::Gnu;
        }

        return str_contains($linkResult, 'musl') ? self::Musl : self::Gnu;
    }
}
