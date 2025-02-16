<?php

declare(strict_types = 1);

namespace Pamald\PamaldComposer\Tests\Unit;

use Pamald\PamaldComposer\NormalPackage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(NormalPackage::class)]
class NormalPackageTest extends TestBase
{
    #[Test]
    public function testGetters(): void
    {
        $lockEntry = [
            'name' => 'a/b',
            'version' => '1.2.3',
        ];
        $package = new NormalPackage(
            $lockEntry,
            'prod',
            [],
            '^1.2',
        );

        static::assertSame('a/b', $package->name());
        static::assertSame(null, $package->type());
        static::assertSame('1.2.3', $package->versionString());
        static::assertSame(true, $package->isDirectDependency());
        static::assertSame(null, $package->issueTracker());
        static::assertSame('prod', $package->typeOfRelationship());
        static::assertSame('1.2.3', (string) $package->version());
        static::assertSame(null, $package->homepage());
        static::assertSame(null, $package->vcsInfo());
    }
}
