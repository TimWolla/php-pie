<?php

declare(strict_types=1);

namespace Php\Pie\SelfManage\BuildTools;

use Php\Pie\Platform\TargetPhp\PhpizePath;
use Symfony\Component\Process\ExecutableFinder;

use function is_array;

/** @internal This is not public API for PIE, so should not be depended upon unless you accept the risk of BC breaks */
class PhpizeBuildToolFinder extends BinaryBuildToolFinder
{
    public function check(): bool
    {
        $tools = is_array($this->tool) ? $this->tool : [$this->tool];

        foreach ($tools as $tool) {
            $foundTool = (new ExecutableFinder())->find($tool);

            if ($foundTool !== null && PhpizePath::looksLikeValidPhpize($foundTool)) {
                return true;
            }
        }

        return false;
    }
}
