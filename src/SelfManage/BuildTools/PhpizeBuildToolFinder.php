<?php

declare(strict_types=1);

namespace Php\Pie\SelfManage\BuildTools;

use Php\Pie\Platform\PackageManager;
use Php\Pie\Platform\TargetPhp\PhpizePath;
use Php\Pie\Platform\TargetPlatform;
use RuntimeException;
use Symfony\Component\Process\ExecutableFinder;

use function file_exists;
use function is_array;
use function is_executable;

/** @internal This is not public API for PIE, so should not be depended upon unless you accept the risk of BC breaks */
class PhpizeBuildToolFinder extends BinaryBuildToolFinder
{
    /** @param array<PackageManager::*, non-empty-string|null> $packageManagerPackages */
    public function __construct(
        array $packageManagerPackages,
    ) {
        parent::__construct('phpize', $packageManagerPackages);
    }

    public function check(TargetPlatform $targetPlatform): bool
    {
        $tools = is_array($this->tool) ? $this->tool : [$this->tool];

        if ($targetPlatform->phpizePath !== null) {
            $tools[] = $targetPlatform->phpizePath->phpizeBinaryPath;
        }

        try {
            $tools[] = PhpizePath::guessFrom($targetPlatform->phpBinaryPath)->phpizeBinaryPath;
        } catch (RuntimeException) {
            // intentionally ignored - just don't try to use the guessed phpize path
        }

        foreach ($tools as $tool) {
            if (file_exists($tool) && is_executable($tool) && PhpizePath::looksLikeValidPhpize($tool)) {
                return true;
            }

            $foundTool = (new ExecutableFinder())->find($tool);

            if ($foundTool !== null && PhpizePath::looksLikeValidPhpize($foundTool)) {
                return true;
            }
        }

        return false;
    }
}
