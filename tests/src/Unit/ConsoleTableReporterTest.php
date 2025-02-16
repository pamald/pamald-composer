<?php

declare(strict_types = 1);

namespace Pamald\PamaldComposer\Tests\Unit;

use Pamald\Pamald\LockDiffEntry;
use Pamald\Pamald\LockDiffer;
use Pamald\Pamald\Reporter\ConsoleTableReporter;
use Pamald\PamaldComposer\NormalPackage;
use Pamald\PamaldComposer\PackageCollector;
use Pamald\PamaldComposer\PhpCorePackage;
use Pamald\PamaldComposer\PhpExtPackage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Sweetchuck\Utils\Filter\CustomFilter;

#[CoversClass(PackageCollector::class)]
#[CoversClass(PhpCorePackage::class)]
#[CoversClass(PhpExtPackage::class)]
#[CoversClass(NormalPackage::class)]
class ConsoleTableReporterTest extends TestBase
{

    /**
     * @var resource[]
     */
    protected array $streams = [];

    protected function tearDown(): void
    {
        foreach ($this->streams as $stream) {
            fclose($stream);
        }
        $this->streams = [];

        parent::tearDown();
    }

    /**
     * @return array<string, mixed[]>
     */
    public static function casesGenerate(): array
    {
        return [
            'basic' => [
                'expected' => <<< 'TEXT'
                    +------+-----------+-----------+----------------+----------------+---------+---------+
                    | Name | L Version | R Version | L Relationship | R Relationship | L Depth | R Depth |
                    +------+-----------+-----------+----------------+----------------+---------+---------+
                    | Direct prod                                                                        |
                    | a/b  | 2.1.1     | 2.2.2     | prod           | prod           | direct  | direct  |
                    | Direct dev                                                                         |
                    | b/a  | 4.1.1     | 4.2.2     | dev            | dev            | direct  | direct  |
                    | b/b  | 5.1.1     | 5.2.2     | dev            | dev            | direct  | direct  |
                    | Other                                                                              |
                    | c/a  | 3.1.1     | 3.2.2     | prod           | prod           | child   | child   |
                    +------+-----------+-----------+----------------+----------------+---------+---------+

                    TEXT,
                'leftLock' => [
                    'packages' => [
                        [
                            'name' => 'a/a',
                            'version' => '1.1.1',
                        ],
                        [
                            'name' => 'a/b',
                            'version' => '2.1.1',
                        ],
                        [
                            'name' => 'c/a',
                            'version' => '3.1.1',
                        ],
                    ],
                    'packages-dev' => [
                        [
                            'name' => 'b/a',
                            'version' => '4.1.1',
                        ],
                        [
                            'name' => 'b/b',
                            'version' => '5.1.1',
                        ],
                        [
                            'name' => 'd/a',
                            'version' => '6.1.1',
                        ],
                    ],
                ],
                'leftJson' => [
                    'require' => [
                        'a/a' => '^1.0',
                        'a/b' => '^2.0',
                    ],
                    'require-dev' => [
                        'b/a' => '^4.0',
                        'b/b' => '^5.0',
                    ],
                ],
                'rightLock' => [
                    'packages' => [
                        [
                            'name' => 'a/a',
                            'version' => '1.1.1',
                        ],
                        [
                            'name' => 'a/b',
                            'version' => '2.2.2',
                        ],
                        [
                            'name' => 'c/a',
                            'version' => '3.2.2',
                        ],
                    ],
                    'packages-dev' => [
                        [
                            'name' => 'b/a',
                            'version' => '4.2.2',
                        ],
                        [
                            'name' => 'b/b',
                            'version' => '5.2.2',
                        ],
                        [
                            'name' => 'd/a',
                            'version' => '6.1.1',
                        ],
                    ],
                ],
                'rightJson' => [
                    'require' => [
                        'a/a' => '^1.0',
                        'a/b' => '^2.0',
                    ],
                    'require-dev' => [
                        'b/a' => '^4.0',
                        'b/b' => '^5.0',
                    ],
                ],
                'options' => [
                    'groups' => [
                        'direct-prod' => [
                            'enabled' => true,
                            'id' => 'direct-prod',
                            'title' => 'Direct prod',
                            'weight' => 0,
                            'showEmpty' => false,
                            'emptyContent' => '-- empty --',
                            'filter' => (new CustomFilter())
                                ->setOperator(function (LockDiffEntry $entry): bool {
                                    return $entry->right?->isDirectDependency()
                                        && $entry->right->typeOfRelationship() === 'prod';
                                }),
                            'comparer' => null,
                        ],
                        'direct-dev' => [
                            'enabled' => true,
                            'id' => 'direct-dev',
                            'title' => 'Direct dev',
                            'weight' => 1,
                            'showEmpty' => false,
                            'emptyContent' => '-- empty --',
                            'filter' => (new CustomFilter())
                                ->setOperator(function (LockDiffEntry $entry): bool {
                                    return $entry->right?->isDirectDependency()
                                        && $entry->right->typeOfRelationship() === 'dev';
                                }),
                            'comparer' => null,
                        ],
                        'other' => [
                            'enabled' => true,
                            'id' => 'other',
                            'title' => 'Other',
                            'weight' => 999,
                            'showEmpty' => false,
                            'emptyContent' => '-- empty --',
                            'filter' => null,
                            'comparer' => null,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param null|array<string, mixed> $leftLock
     * @param null|array<string, mixed> $leftJson
     * @param null|array<string, mixed> $rightLock
     * @param null|array<string, mixed> $rightJson
     * @phpstan-param pamald-console-table-reporter-options $options
     */
    #[Test]
    #[DataProvider('casesGenerate')]
    public function testGenerate(
        string $expected,
        ?array $leftLock = null,
        ?array $leftJson = null,
        ?array $rightLock = null,
        ?array $rightJson = null,
        array $options = [],
    ): void {
        if (!isset($options['stream'])) {
            $options['stream'] = static::createStream();
        }
        $this->streams[] = $options['stream'];

        $packageCollector = new PackageCollector();
        $differ = new LockDiffer();
        $entries = $differ->diff(
            $packageCollector->collect($leftLock, $leftJson),
            $packageCollector->collect($rightLock, $rightJson),
        );
        (new ConsoleTableReporter())
            ->setOptions($options)
            ->generate($entries);
        rewind($options['stream']);
        static::assertSame(
            $expected,
            stream_get_contents($options['stream']),
        );
    }

    /**
     * @return resource
     */
    protected static function createStream()
    {
        $filePath = 'php://memory';
        $resource = fopen($filePath, 'rw');
        if ($resource === false) {
            throw new \RuntimeException("file $filePath could not be opened");
        }

        return $resource;
    }
}
