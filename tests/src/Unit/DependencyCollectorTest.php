<?php

declare(strict_types = 1);

namespace Pamald\PamaldComposer\Tests\Unit;

use Pamald\Pamald\DependencyEnvironment;
use Pamald\Pamald\DependencyLink;
use Pamald\Pamald\DependencyType;
use Pamald\PamaldComposer\DependencyCollector;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(DependencyCollector::class)]
class DependencyCollectorTest extends TestBase
{

    /**
     * @return array<string, mixed>
     */
    public static function casesCollect(): array
    {
        return [
            'empty' => [
                'expected' => [],
                'lock' => [],
                'json' => null,
            ],
            'basic' => [
                'expected' => [
                    'php' => [
                        'name' => 'php',
                        'type' => DependencyType::Platform,
                        'link' =>DependencyLink::Required,
                        'environment' => DependencyEnvironment::Production,
                        'versionString' => '>=8.3',
                        'isDirectDependency' => true,
                    ],
                    'ext-imagick' => [
                        'name' => 'ext-imagick',
                        'type' => DependencyType::Platform,
                        'link' =>DependencyLink::Required,
                        'environment' => DependencyEnvironment::Production,
                        'versionString' => '*',
                        'isDirectDependency' => true,
                    ],
                    'a/b' => [
                        'name' => 'a/b',
                        'type' => DependencyType::Package,
                        'link' =>DependencyLink::Required,
                        'environment' => DependencyEnvironment::Production,
                        'versionString' => '1.2.3',
                        'isDirectDependency' => true,
                    ],
                    'a/c' => [
                        'name' => 'a/c',
                        'type' => DependencyType::Package,
                        'link' =>DependencyLink::Required,
                        'environment' => DependencyEnvironment::Development,
                        'versionString' => '3.4.0',
                        'isDirectDependency' => true,
                    ],
                ],
                'lock' => [
                    'packages' => [
                        [
                            'name' => 'a/b',
                            'version' => '1.2.3',
                        ],
                    ],
                    'packages-dev' => [
                        [
                            'name' => 'a/c',
                            'version' => '3.4.0',
                        ],
                    ],
                ],
                'json' => [
                    'require' => [
                        'php' => '>=8.3',
                        'ext-imagick' => '*',
                        'a/b' => '^1.2',
                    ],
                    'require-dev' => [
                        'a/c' => '^3.4',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $expected
     * @param array<string, mixed> $lock
     * @param array<string, mixed> $json
     */
    #[Test]
    #[DataProvider('casesCollect')]
    public function testCollect(array $expected, array $lock, ?array $json): void
    {
        $collector = new DependencyCollector();

        /** @var array<string, \Pamald\Pamald\DependencyInterface&\JsonSerializable> $actual */
        $actual = $collector->collect($lock, $json);

        static::assertCount(
            count($expected),
            $actual,
            'actual has the right amount of items',
        );
        foreach ($expected as $expectedName => $expectedValues) {
            static::assertArrayHasKey($expectedName, $actual);
            $this->assertSame(
                $expectedValues,
                $actual[$expectedName]->jsonSerialize(),
            );
        }
    }
}
