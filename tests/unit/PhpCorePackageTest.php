<?php

declare(strict_types = 1);

namespace Pamald\PamaldComposer\Tests\Unit;

use Pamald\PamaldComposer\PhpCorePackage;

/**
 * @covers \Pamald\PamaldComposer\PhpCorePackage
 */
class PhpCorePackageTest extends TestBase
{

    public function testGetters(): void
    {
        $package = new PhpCorePackage(
            'prod',
            '>=8.3',
        );

        $this->tester->assertSame('php', $package->name());
        $this->tester->assertSame(null, $package->type());
        $this->tester->assertSame('>=8.3', $package->versionString());
        $this->tester->assertSame(true, $package->isDirectDependency());
        $this->tester->assertSame(null, $package->issueTracker());
        $this->tester->assertSame('prod', $package->typeOfRelationship());
        $this->tester->assertSame(null, $package->version());
        $this->tester->assertSame(null, $package->homepage());
        $this->tester->assertSame(null, $package->vcsInfo());
    }
}
