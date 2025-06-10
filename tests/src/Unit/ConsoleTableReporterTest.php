<?php

declare(strict_types = 1);

namespace Pamald\PamaldComposer\Tests\Unit;

use Pamald\Pamald\LockDiffer;
use Pamald\Pamald\Reporter\ConsoleTableReporter;
use Pamald\PamaldComposer\PackageDependency;
use Pamald\PamaldComposer\DependencyCollector;
use Pamald\PamaldComposer\PhpCoreDependency;
use Pamald\PamaldComposer\PhpExtDependency;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * @phpstan-import-type PamaldConsoleTableReporterOptions from \Pamald\Pamald\Phpstan
 */
#[CoversClass(DependencyCollector::class)]
#[CoversClass(PhpCoreDependency::class)]
#[CoversClass(PhpExtDependency::class)]
#[CoversClass(PackageDependency::class)]
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
                // phpcs:disable Generic.Files.LineLength.TooLong
                'expected' => <<< 'TEXT'
                    +------+-----------+-----------+---------+---------+----------+----------+-------------+-------------+---------+---------+
                    | Name | L Version | R Version | L Type  | R Type  | L Link   | R Link   | L Env       | R Env       | L Depth | R Depth |
                    +------+-----------+-----------+---------+---------+----------+----------+-------------+-------------+---------+---------+
                    | Production - Direct                                                                                                    |
                    | a/b  | 2.1.1     | 2.2.2     | package | package | required | required | production  | production  | direct  | direct  |
                    | Production - Indirect                                                                                                  |
                    | c/a  | 3.1.1     | 3.2.2     | package | package | required | required | production  | production  | child   | child   |
                    | Development - Direct                                                                                                   |
                    | b/a  | 4.1.1     | 4.2.2     | package | package | required | required | development | development | direct  | direct  |
                    | b/b  | 5.1.1     | 5.2.2     | package | package | required | required | development | development | direct  | direct  |
                    +------+-----------+-----------+---------+---------+----------+----------+-------------+-------------+---------+---------+

                    TEXT,
                // phpcs:enable Generic.Files.LineLength.TooLong
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
                'options' => [],
            ],
        ];
    }

    /**
     * @param null|array<string, mixed> $leftLock
     * @param null|array<string, mixed> $leftJson
     * @param null|array<string, mixed> $rightLock
     * @param null|array<string, mixed> $rightJson
     * @phpstan-param PamaldConsoleTableReporterOptions $options
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

        $packageCollector = new DependencyCollector();
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
