<?php

declare(strict_types=1);

namespace Php\PieUnitTest\ComposerIntegration\Listeners;

use Composer\Package\CompletePackageInterface;
use Php\Pie\ComposerIntegration\Listeners\CouldNotDetermineDownloadUrlMethod;
use Php\Pie\DependencyResolver\Package;
use Php\Pie\Downloading\DownloadUrlMethod;
use Php\Pie\ExtensionName;
use Php\Pie\ExtensionType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CouldNotDetermineDownloadUrlMethod::class)]
final class CouldNotDetermineDownloadUrlMethodTest extends TestCase
{
    public function testSingleDownloadUrlMethod(): void
    {
        $package = new Package(
            $this->createMock(CompletePackageInterface::class),
            ExtensionType::PhpModule,
            ExtensionName::normaliseFromString('foo'),
            'foo/foo',
            '1.2.3',
            null,
        );

        $e = CouldNotDetermineDownloadUrlMethod::fromDownloadUrlMethods(
            $package,
            [DownloadUrlMethod::PrePackagedBinary],
            [DownloadUrlMethod::PrePackagedBinary->value => 'A bad thing happened downloading the binary'],
        );

        self::assertSame(
            'Could not download foo/foo using pre-packaged-binary method: A bad thing happened downloading the binary',
            $e->getMessage(),
        );
    }

    public function testMultipleDownloadUrlMethods(): void
    {
        $package = new Package(
            $this->createMock(CompletePackageInterface::class),
            ExtensionType::PhpModule,
            ExtensionName::normaliseFromString('foo'),
            'foo/foo',
            '1.2.3',
            null,
        );

        $e = CouldNotDetermineDownloadUrlMethod::fromDownloadUrlMethods(
            $package,
            [
                DownloadUrlMethod::PrePackagedBinary,
                DownloadUrlMethod::PrePackagedSourceDownload,
            ],
            [
                DownloadUrlMethod::PrePackagedBinary->value => 'A bad thing happened downloading the binary',
                DownloadUrlMethod::PrePackagedSourceDownload->value => 'Another bad thing happened downloading the source',
            ],
        );

        self::assertSame(
            'Could not download foo/foo using the following methods:
 - pre-packaged-binary: A bad thing happened downloading the binary
 - pre-packaged-source: Another bad thing happened downloading the source
',
            $e->getMessage(),
        );
    }
}
