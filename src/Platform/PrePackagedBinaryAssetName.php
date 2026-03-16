<?php

declare(strict_types=1);

namespace Php\Pie\Platform;

use Php\Pie\DependencyResolver\Package;

use function array_unique;
use function array_values;
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
        $debug = $targetPlatform->phpBinaryPath->debugMode() === DebugBuild::Debug ? '-debug' : '';
        $tsNoSuffix = $targetPlatform->threadSafety === ThreadSafetyMode::ThreadSafe ? '-zts' : '';
        $tsWithSuffix = $targetPlatform->threadSafety === ThreadSafetyMode::ThreadSafe ? '-zts' : '-nts';
        $libc = $targetPlatform->libcFlavour()->value;

        $name    = $package->extensionName()->name();
        $version = $package->version();
        $phpVer  = $targetPlatform->phpBinaryPath->majorMinorVersion();
        $arch    = $targetPlatform->architecture->name;
        $os      = $targetPlatform->operatingSystemFamily->value;

        $names = [
            strtolower(sprintf('php_%s-%s_php%s-%s-%s-%s%s%s.zip', $name, $version, $phpVer, $arch, $os, $libc, $debug, $tsNoSuffix)),
            strtolower(sprintf('php_%s-%s_php%s-%s-%s-%s%s%s.tgz', $name, $version, $phpVer, $arch, $os, $libc, $debug, $tsNoSuffix)),
            strtolower(sprintf('php_%s-%s_php%s-%s-%s-%s%s%s.zip', $name, $version, $phpVer, $arch, $os, $libc, $debug, $tsWithSuffix)),
            strtolower(sprintf('php_%s-%s_php%s-%s-%s-%s%s%s.tgz', $name, $version, $phpVer, $arch, $os, $libc, $debug, $tsWithSuffix)),
        ];

        // Fallbacks with anylibc suffix, for unified binaries compatible with both glibc and musl (Linux only)
        if ($targetPlatform->operatingSystemFamily === OperatingSystemFamily::Linux) {
            $names[] = strtolower(sprintf('php_%s-%s_php%s-%s-%s-anylibc%s%s.zip', $name, $version, $phpVer, $arch, $os, $debug, $tsNoSuffix));
            $names[] = strtolower(sprintf('php_%s-%s_php%s-%s-%s-anylibc%s%s.tgz', $name, $version, $phpVer, $arch, $os, $debug, $tsNoSuffix));
            $names[] = strtolower(sprintf('php_%s-%s_php%s-%s-%s-anylibc%s%s.zip', $name, $version, $phpVer, $arch, $os, $debug, $tsWithSuffix));
            $names[] = strtolower(sprintf('php_%s-%s_php%s-%s-%s-anylibc%s%s.tgz', $name, $version, $phpVer, $arch, $os, $debug, $tsWithSuffix));
        }

        return array_values(array_unique($names));
    }
}
