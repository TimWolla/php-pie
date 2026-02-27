<?php

declare(strict_types=1);

namespace Php\PieUnitTest\Command;

use Php\Pie\Command\InvokeSubCommand;
use Php\Pie\Util\OutputFormatterWithPrefix;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

use function trim;

#[CoversClass(InvokeSubCommand::class)]
final class InvokeSubCommandTest extends TestCase
{
    public function testInvokeWithNoOutputFormatterRunsSubCommand(): void
    {
        $inputDefinition = new InputDefinition();
        $inputDefinition->addOption(new InputOption('verbose', 'v', InputOption::VALUE_NONE, 'Verbose option'));
        $input = new ArrayInput(['--verbose' => true], $inputDefinition);

        $output = new BufferedOutput();

        $application = $this->createMock(Application::class);
        $application->expects(self::once())
            ->method('doRun')
            ->willReturnCallback(static function (ArrayInput $newInput, OutputInterface $output) {
                self::assertSame('foo --verbose=1', (string) $newInput);
                $output->writeln('command output here');

                return 0;
            });

        $command = $this->createMock(Command::class);
        $command->method('getApplication')->willReturn($application);

        $invoker = new InvokeSubCommand($output);
        self::assertSame(0, ($invoker)($command, ['command' => 'foo'], $input));
        self::assertSame('command output here', trim($output->fetch()));
    }

    public function testInvokeWithPrefixOutputFormatterRunsSubCommand(): void
    {
        $inputDefinition = new InputDefinition();
        $inputDefinition->addOption(new InputOption('verbose', 'v', InputOption::VALUE_NONE, 'Verbose option'));
        $input = new ArrayInput(['--verbose' => true], $inputDefinition);

        $output = new BufferedOutput();

        $application = $this->createMock(Application::class);
        $application->expects(self::once())
            ->method('doRun')
            ->willReturnCallback(static function (ArrayInput $newInput, OutputInterface $output) {
                self::assertSame('foo --verbose=1', (string) $newInput);
                $output->writeln('command output here');

                return 0;
            });

        $command = $this->createMock(Command::class);
        $command->method('getApplication')->willReturn($application);

        $invoker = new InvokeSubCommand($output);
        self::assertSame(0, ($invoker)($command, ['command' => 'foo'], $input, new OutputFormatterWithPrefix('prefix> ')));
        self::assertSame('prefix> command output here', trim($output->fetch()));
    }
}
