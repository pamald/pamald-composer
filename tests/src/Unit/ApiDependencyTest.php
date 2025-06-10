<?php

declare(strict_types = 1);

namespace Pamald\PamaldComposer\Tests\Unit;

use Pamald\Pamald\DependencyEnvironment;
use Pamald\Pamald\DependencyLink;
use Pamald\Pamald\DependencyType;
use Pamald\PamaldComposer\ApiDependency;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(ApiDependency::class)]
class ApiDependencyTest extends TestBase
{
    #[Test]
    public function testGetters(): void
    {
        $dependency = new ApiDependency(
            'composer-plugin-api',
            DependencyLink::Required,
            DependencyEnvironment::Development,
            '*',
        );

        static::assertSame('composer-plugin-api', $dependency->name());
        static::assertEquals(DependencyType::API, $dependency->type());
        static::assertEquals(DependencyLink::Required, $dependency->link());
        static::assertEquals(DependencyEnvironment::Development, $dependency->environment());
        static::assertSame('*', $dependency->versionString());
        static::assertSame(true, $dependency->isDirectDependency());
        static::assertSame(null, $dependency->issueTracker());
        static::assertSame(null, $dependency->version());
        static::assertSame(null, $dependency->homepage());
        static::assertSame(null, $dependency->vcsInfo());
    }
}
