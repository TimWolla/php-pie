<?php

declare(strict_types=1);

namespace Php\Pie\DependencyResolver\DependencyInstaller;

use Php\Pie\Platform\PackageManager;

class SystemDependenciesDefinition
{
    /** @param array<non-empty-string, array<non-empty-string, non-empty-string>> $definition */
    public function __construct(public readonly array $definition)
    {
    }

    /**
     * Checks for the existence of these libraries should be added into
     * {@see \Php\Pie\ComposerIntegration\PhpBinaryPathBasedPlatformRepository::addLibrariesUsingPkgConfig()}
     */
    public static function default(): self
    {
        return new self([
            'sodium' => [
                PackageManager::Apt->value => 'libsodium-dev',
                PackageManager::Apk->value => 'libsodium-dev',
                PackageManager::Dnf->value => 'pkgconfig(libsodium)',
                PackageManager::Yum->value => 'pkgconfig(libsodium)',
            ],
            'jpeg' => [
                PackageManager::Apt->value => 'libjpeg-dev',
                PackageManager::Apk->value => 'libjpeg-turbo-dev',
                PackageManager::Dnf->value => 'pkgconfig(libjpeg)',
                PackageManager::Yum->value => 'pkgconfig(libjpeg)',
            ],
        ]);
    }
}
