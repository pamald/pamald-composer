<?php

declare(strict_types = 1);

namespace Pamald\PamaldComposer\Tests\Unit;

use Pamald\Pamald\DependencyEnvironment;
use Pamald\Pamald\DependencyLink;
use Pamald\Pamald\DependencyType;
use Pamald\PamaldComposer\PhpExtDependency;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(PhpExtDependency::class)]
class PhpExtDependencyTest extends TestBase
{
    #[Test]
    public function testGetters(): void
    {
        $dependency = new PhpExtDependency(
            'ext-imagick',
            DependencyLink::Required,
            DependencyEnvironment::Development,
            '*',
        );

        static::assertSame('ext-imagick', $dependency->name());
        static::assertEquals(DependencyType::Platform, $dependency->type());
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
