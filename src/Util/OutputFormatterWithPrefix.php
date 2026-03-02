<?php

declare(strict_types=1);

namespace Php\Pie\Util;

use Composer\Factory;
use Symfony\Component\Console\Formatter\OutputFormatter;

class OutputFormatterWithPrefix extends OutputFormatter
{
    /**
     * @param non-empty-string $linePrefix
     *
     * @inheritDoc
     */
    public function __construct(private readonly string $linePrefix, bool $decorated = false, array $styles = [])
    {
        parent::__construct($decorated, $styles);
    }

    /** @param non-empty-string $linePrefix */
    public static function newWithPrefix(string $linePrefix): self
    {
        return new self($linePrefix, false, Factory::createAdditionalStyles());
    }

    public function format(string|null $message): string|null
    {
        $formatted = parent::format($message);

        if ($formatted === null) {
            return null;
        }

        return $this->linePrefix . $formatted;
    }
}
