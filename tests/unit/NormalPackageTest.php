<?php

declare(strict_types = 1);

namespace Pamald\PamaldComposer\Tests\Unit;

use Pamald\PamaldComposer\NormalPackage;

/**
 * @covers \Pamald\PamaldComposer\NormalPackage
 */
class NormalPackageTest extends TestBase
{
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

        $this->tester->assertSame('a/b', $package->name());
        $this->tester->assertSame(null, $package->type());
        $this->tester->assertSame('1.2.3', $package->versionString());
        $this->tester->assertSame(true, $package->isDirectDependency());
        $this->tester->assertSame(null, $package->issueTracker());
        $this->tester->assertSame('prod', $package->typeOfRelationship());
        $this->tester->assertSame('1.2.3', (string) $package->version());
        $this->tester->assertSame(null, $package->homepage());
        $this->tester->assertSame(null, $package->vcsInfo());
    }
}
