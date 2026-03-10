<?php

declare(strict_types=1);

namespace Php\Pie\Platform\TargetPhp\Exception;

use Php\Pie\Platform\TargetPhp\PhpBinaryPath;
use RuntimeException;

use function file_exists;
use function is_dir;

class ExtensionPathProblem extends RuntimeException
{
    public static function new(PhpBinaryPath $php, string|null $extensionPath): self
    {
        $message = 'Could not determine extension path for ' . $php->phpBinaryPath;

        if ($extensionPath === null) {
            $message .= '; extension_dir => not set';
        } else {
            $message .= '; extension_dir => ' . $extensionPath;

            if (file_exists($extensionPath)) {
                $message .= '; exists';

                if (is_dir($extensionPath)) {
                    $message .= ', is a directory';
                } else {
                    $message .= ', not a directory';
                }
            } else {
                $message .= '; does not exist';
            }
        }

        return new self($message);
    }
}
