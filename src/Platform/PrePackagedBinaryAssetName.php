<?php

declare(strict_types=1);

namespace Php\Pie\Platform;

use Php\Pie\DependencyResolver\Package;

use function sprintf;
use function strtolower;

/** @internal This is not public API for PIE, so should not be depended upon unless you accept the risk of BC breaks */
final class PrePackagedBinaryAssetName
{
    private function __construct()
    {
    }

    /** @return non-empty-list<non-empty-string> */
    public static function packageNames(TargetPlatform $targetPlatform, Package $package): array
    {
        return [
            strtolower(sprintf( // @todo 436 - confirm naming; check if compatible with existing packages
                'php_%s-%s_php%s-%s-%s-%s-%s.tgz',
                $package->extensionName()->name(),
                $package->version(),
                $targetPlatform->phpBinaryPath->majorMinorVersion(),
                $targetPlatform->architecture->name,
                LibcFlavour::Gnu->value, // @todo 436 - detect libc flavour
                DebugBuild::Debug->value, // @todo 436 - detect debug mode
                $targetPlatform->threadSafety->asShort(),
            )),
        ];
    }
}
