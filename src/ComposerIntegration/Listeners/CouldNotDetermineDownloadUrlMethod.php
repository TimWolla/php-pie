<?php

declare(strict_types=1);

namespace Php\Pie\ComposerIntegration\Listeners;

use Php\Pie\DependencyResolver\Package;
use Php\Pie\Downloading\DownloadUrlMethod;
use RuntimeException;

use function array_key_first;
use function count;
use function sprintf;

use const PHP_EOL;

class CouldNotDetermineDownloadUrlMethod extends RuntimeException
{
    /**
     * @param non-empty-list<DownloadUrlMethod> $downloadMethods
     * @param array<string, string>             $failureReasons
     */
    public static function fromDownloadUrlMethods(Package $piePackage, array $downloadMethods, array $failureReasons): self
    {
        $message = sprintf('Could not download %s', $piePackage->name());

        if (count($downloadMethods) === 1) {
            $first    = array_key_first($downloadMethods);
            $message .= sprintf(' using %s method: %s', $downloadMethods[$first]->value, $failureReasons[$downloadMethods[$first]->value] ?? '(unknown failure)');

            return new self($message);
        }

        $message .= ' using the following methods:' . PHP_EOL;

        foreach ($downloadMethods as $downloadMethod) {
            $message .= sprintf(' - %s: %s%s', $downloadMethod->value, $failureReasons[$downloadMethod->value] ?? '(unknown failure)', PHP_EOL);
        }

        return new self($message);
    }
}
