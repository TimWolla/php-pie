<?php

declare(strict_types=1);

namespace Php\Pie\Platform;

/** @internal This is not public API for PIE, so should not be depended upon unless you accept the risk of BC breaks */
enum LibcFlavour: string
{
    case Gnu  = 'glibc';
    case Musl = 'musl';
}
