<?php

declare(strict_types = 1);

namespace Pamald\PamaldComposer\Tests\Unit;

use Pamald\PamaldComposer\PhpCorePackage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(PhpCorePackage::class)]
class PhpCorePackageTest extends TestBase
{

    #[Test]
    public function testGetters(): void
    {
        $package = new PhpCorePackage(
            'prod',
            '>=8.3',
        );

        static::assertSame('php', $package->name());
        static::assertSame(null, $package->type());
        static::assertSame('>=8.3', $package->versionString());
        static::assertSame(true, $package->isDirectDependency());
        static::assertSame(null, $package->issueTracker());
        static::assertSame('prod', $package->typeOfRelationship());
        static::assertSame(null, $package->version());
        static::assertSame(null, $package->homepage());
        static::assertSame(null, $package->vcsInfo());
    }
}
