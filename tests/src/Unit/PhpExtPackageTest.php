<?php

declare(strict_types = 1);

namespace Pamald\PamaldComposer\Tests\Unit;

use Pamald\PamaldComposer\PhpExtPackage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(PhpExtPackage::class)]
class PhpExtPackageTest extends TestBase
{
    #[Test]
    public function testGetters(): void
    {
        $package = new PhpExtPackage(
            'ext-imagick',
            'prod',
            '*',
        );

        static::assertSame('ext-imagick', $package->name());
        static::assertSame(null, $package->type());
        static::assertSame('*', $package->versionString());
        static::assertSame(true, $package->isDirectDependency());
        static::assertSame(null, $package->issueTracker());
        static::assertSame('prod', $package->typeOfRelationship());
        static::assertSame(null, $package->version());
        static::assertSame(null, $package->homepage());
        static::assertSame(null, $package->vcsInfo());
    }
}
