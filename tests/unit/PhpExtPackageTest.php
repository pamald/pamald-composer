<?php

declare(strict_types = 1);

namespace Pamald\PamaldComposer\Tests\Unit;

use Pamald\PamaldComposer\PhpExtPackage;

/**
 * @covers \Pamald\PamaldComposer\PhpExtPackage
 */
class PhpExtPackageTest extends TestBase
{
    public function testGetters(): void
    {
        $package = new PhpExtPackage(
            'ext-imagick',
            'prod',
            '*',
        );

        $this->tester->assertSame('ext-imagick', $package->name());
        $this->tester->assertSame(null, $package->type());
        $this->tester->assertSame('*', $package->versionString());
        $this->tester->assertSame(true, $package->isDirectDependency());
        $this->tester->assertSame(null, $package->issueTracker());
        $this->tester->assertSame('prod', $package->typeOfRelationship());
        $this->tester->assertSame(null, $package->version());
        $this->tester->assertSame(null, $package->homepage());
        $this->tester->assertSame(null, $package->vcsInfo());
    }
}
