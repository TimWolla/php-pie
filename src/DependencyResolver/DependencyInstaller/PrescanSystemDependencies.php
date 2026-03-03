<?php

declare(strict_types=1);

namespace Php\Pie\DependencyResolver\DependencyInstaller;

use Composer\Composer;
use Composer\IO\IOInterface;
use Php\Pie\DependencyResolver\DependencyResolver;
use Php\Pie\DependencyResolver\DependencyStatus;
use Php\Pie\DependencyResolver\FetchDependencyStatuses;
use Php\Pie\DependencyResolver\RequestedPackageAndVersion;
use Php\Pie\Platform\PackageManager;
use Php\Pie\Platform\TargetPlatform;
use Throwable;

use function array_filter;
use function array_key_exists;
use function array_map;
use function array_unique;
use function array_values;
use function count;
use function implode;
use function sprintf;
use function str_replace;

/** @internal This is not public API for PIE, so should not be depended upon unless you accept the risk of BC breaks */
class PrescanSystemDependencies
{
    /** @var array<non-empty-string, array<non-empty-string, non-empty-string>> */
    private readonly array $libraries;

    public function __construct(
        private readonly DependencyResolver $dependencyResolver,
        private readonly FetchDependencyStatuses $fetchDependencyStatuses,
        private readonly IOInterface $io,
        private readonly PackageManager|null $packageManager,
    ) {
        /**
         * Checks for the existence of these libraries should be added into
         * {@see \Php\Pie\ComposerIntegration\PhpBinaryPathBasedPlatformRepository::addLibrariesUsingPkgConfig()}
         */
        $this->libraries = [
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
        ];
    }

    public function __invoke(Composer $composer, TargetPlatform $targetPlatform, RequestedPackageAndVersion $requestedNameAndVersion): void
    {
        if ($this->packageManager === null) {
            $this->io->writeError('<comment>Skipping pre-scan of system dependencies, as a supported package manager could not be detected.</comment>', verbosity: IOInterface::VERBOSE);

            return;
        }

        $this->io->write(sprintf('Checking system dependencies are present for extension %s', $requestedNameAndVersion->prettyNameAndVersion()), verbosity: IOInterface::VERBOSE);

        $package = ($this->dependencyResolver)(
            $composer,
            $targetPlatform,
            $requestedNameAndVersion,
            true,
        );

        $unmetDependencies = array_filter(
            ($this->fetchDependencyStatuses)($targetPlatform, $composer, $package->composerPackage()),
            static function (DependencyStatus $dependencyStatus): bool {
                return ! $dependencyStatus->satisfied();
            },
        );

        if (! count($unmetDependencies)) {
            $this->io->write('All system dependencies are already installed.', verbosity: IOInterface::VERBOSE);

            return;
        }

        $this->io->write(
            sprintf('Extension %s has unmet dependencies: %s', $requestedNameAndVersion->prettyNameAndVersion(), implode(', ', array_map(static fn (DependencyStatus $status): string => $status->name, $unmetDependencies))),
            verbosity: IOInterface::VERBOSE,
        );

        $packageManagerPackages = array_values(array_unique(array_filter(array_map(
            fn (DependencyStatus $unmetDependency): string|null => $this->packageManagerPackageForDependency($unmetDependency, $this->packageManager),
            $unmetDependencies,
        ))));

        if (! count($packageManagerPackages)) {
            $this->io->writeError('No system dependencies could be installed automatically by PIE.', verbosity: IOInterface::VERBOSE);

            return;
        }

        $proposedInstallCommand = implode(' ', $this->packageManager->installCommand($packageManagerPackages));
        $this->io->write(sprintf('<info>Installing missing system dependencies:</info> %s', $proposedInstallCommand));

        try {
            $this->packageManager->install($packageManagerPackages);
        } catch (Throwable $anything) {
            $this->io->writeError(sprintf('<info>Failed to install missing system dependencies:</info> %s', $anything->getMessage()));
        }
    }

    private function packageManagerPackageForDependency(DependencyStatus $unmetDependency, PackageManager $packageManager): string|null
    {
        $depName = str_replace('lib-', '', $unmetDependency->name);

        if (! array_key_exists($depName, $this->libraries)) {
            $this->io->writeError(
                sprintf('Could not automatically install %s, as PIE does not have the package manager definition.', $unmetDependency->name),
                verbosity: IOInterface::VERBOSE,
            );

            return null;
        }

        if (! array_key_exists($packageManager->value, $this->libraries[$depName])) {
            $this->io->writeError(
                sprintf('Could not automatically install "%s", as PIE does not have a definition for "%s"', $unmetDependency->name, $packageManager->value),
                verbosity: IOInterface::VERBOSE,
            );

            return null;
        }

        $packageManagerPackage = $this->libraries[$depName][$packageManager->value];

        // Note: ideally, we should also parse the version constraint. This initial iteration will ignore that, to be improved later.
        $this->io->write(
            sprintf('Adding %s package %s to be installed for %s', $packageManager->value, $packageManagerPackage, $unmetDependency->name),
            verbosity: IOInterface::VERBOSE,
        );

        return $packageManagerPackage;
    }
}
