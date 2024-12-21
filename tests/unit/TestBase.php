<?php

declare(strict_types = 1);

namespace Pamald\PamaldComposer\Tests\Unit;

use Codeception\Test\Unit;
use Pamald\PamaldComposer\Tests\UnitTester;

class TestBase extends Unit
{

    protected UnitTester $tester;

    protected function selfProjectRoot(): string
    {
        return dirname(__DIR__, 2);
    }

    protected function createTempDir(): string
    {
        $dir = $this->randomTempDirName();
        mkdir($dir, 0777 - umask(), true);

        return $dir;
    }

    protected function randomTempDirName(): string
    {
        return implode('/', [
            sys_get_temp_dir(),
            'pamald',
            'pamald',
            'test-' . $this->randomId(),
        ]);
    }

    protected function randomId(): string
    {
        return md5((string) (microtime(true) * rand(0, 10000)));
    }
}
