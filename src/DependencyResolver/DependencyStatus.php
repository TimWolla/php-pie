<?php

declare(strict_types=1);

namespace Php\Pie\DependencyResolver;

use Composer\Semver\Constraint\Constraint;
use Composer\Semver\Constraint\ConstraintInterface;
use Php\Pie\Util\Emoji;

use function sprintf;

/** @internal This is not public API for PIE, so should not be depended upon unless you accept the risk of BC breaks */
final class DependencyStatus
{
    public function __construct(
        public readonly string $name,
        public readonly ConstraintInterface $requireConstraint,
        public readonly Constraint|null $installedVersion,
    ) {
    }

    public function asPrettyString(): string
    {
        $statusTemplate = sprintf('%s: %s %%s', $this->name, $this->requireConstraint->getPrettyString());
        if ($this->installedVersion === null) {
            return sprintf($statusTemplate, Emoji::PROHIBITED . ' (not installed)');
        }

        if (! $this->requireConstraint->matches($this->installedVersion)) {
            return sprintf($statusTemplate, Emoji::PROHIBITED . ' (your version is ' . $this->installedVersion->getVersion() . ')');
        }

        return sprintf($statusTemplate, Emoji::GREEN_CHECKMARK);
    }

    public function satisfied(): bool
    {
        return $this->installedVersion !== null && $this->requireConstraint->matches($this->installedVersion);
    }
}
