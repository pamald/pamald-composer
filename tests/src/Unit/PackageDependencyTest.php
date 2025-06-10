<?php

declare(strict_types = 1);

namespace Pamald\PamaldComposer\Tests\Unit;

use Pamald\Pamald\DependencyEnvironment;
use Pamald\Pamald\DependencyLink;
use Pamald\Pamald\DependencyType;
use Pamald\PamaldComposer\PackageDependency;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(PackageDependency::class)]
class PackageDependencyTest extends TestBase
{
    #[Test]
    public function testGetters(): void
    {
        $lockEntry = [
            'name' => 'a/b',
            'version' => '1.2.3',
        ];
        $dependency = new PackageDependency(
            $lockEntry,
            DependencyLink::Required,
            DependencyEnvironment::Production,
            [],
            '^1.2',
        );

        static::assertSame('a/b', $dependency->name());
        static::assertSame(DependencyType::Package, $dependency->type());
        static::assertSame(DependencyLink::Required, $dependency->link());
        static::assertSame(DependencyEnvironment::Production, $dependency->environment());
        static::assertSame('1.2.3', $dependency->versionString());
        static::assertSame(true, $dependency->isDirectDependency());
        static::assertSame(null, $dependency->issueTracker());
        static::assertSame('1.2.3', (string) $dependency->version());
        static::assertSame(null, $dependency->homepage());
        static::assertSame(null, $dependency->vcsInfo());
    }
}
