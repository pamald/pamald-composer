<?php

declare(strict_types = 1);

namespace Pamald\PamaldComposer\Tests\Unit;

use Pamald\Pamald\DependencyEnvironment;
use Pamald\Pamald\DependencyLink;
use Pamald\Pamald\DependencyType;
use Pamald\PamaldComposer\PhpCoreDependency;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(PhpCoreDependency::class)]
class PhpCoreDependencyTest extends TestBase
{

    #[Test]
    public function testGetters(): void
    {
        $dependency = new PhpCoreDependency(
            DependencyLink::Required,
            DependencyEnvironment::Development,
            '>=8.3',
        );

        static::assertSame('php', $dependency->name());
        static::assertSame(DependencyType::Platform, $dependency->type());
        static::assertSame(DependencyLink::Required, $dependency->link());
        static::assertSame(DependencyEnvironment::Development, $dependency->environment());
        static::assertSame('>=8.3', $dependency->versionString());
        static::assertSame(true, $dependency->isDirectDependency());
        static::assertSame(null, $dependency->issueTracker());
        static::assertSame(null, $dependency->version());
        static::assertSame(null, $dependency->homepage());
        static::assertSame(null, $dependency->vcsInfo());
    }
}
