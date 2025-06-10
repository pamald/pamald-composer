<?php

declare(strict_types = 1);

namespace Pamald\PamaldComposer;

use Pamald\Pamald\DependencyCollectorInterface;
use Pamald\Pamald\DependencyEnvironment;
use Pamald\Pamald\DependencyLink;

/**
 * @todo Support for "alias" or "meta" package type.
 * For example: "composer-plugin-api".
 */
class DependencyCollector implements DependencyCollectorInterface
{

    /**
     * @var array<string, mixed>
     */
    protected array $lock;

    /**
     * @var array<string, mixed>
     */
    protected ?array $json;

    /**
     * @var array<string, \Pamald\Pamald\DependencyInterface>
     */
    protected array $dependencies = [];

    /**
     * {@inheritdoc}
     */
    public function collect(?array $lock, ?array $json): array
    {
        if (!$lock) {
            return [];
        }

        $this->lock = $lock;
        $this->json = $json;
        $this->dependencies = [];

        $this
            ->addPhpCore()
            ->addPhpExtensions()
            ->addApi()
            ->addPackage();

        return $this->dependencies;
    }

    protected function addPhpCore(): static
    {
        if (!empty($this->json['require']['php'])) {
            $this->dependencies['php'] = new PhpCoreDependency(
                DependencyLink::Required,
                DependencyEnvironment::Production,
                $this->json['require']['php'],
            );

            return $this;
        }

        if (!empty($this->lock['platform']['php'])) {
            $this->dependencies['php'] = new PhpCoreDependency(
                DependencyLink::Required,
                DependencyEnvironment::Production,
                $this->lock['platform']['php'],
            );
        }

        return $this;
    }

    protected function addPhpExtensions(): static
    {
        if ($this->json) {
            $this->addPhpExtensionsFromPairs(
                DependencyLink::Required,
                DependencyEnvironment::Production,
                $this->json['require'] ?? [],
            );
            $this->addPhpExtensionsFromPairs(
                DependencyLink::Required,
                DependencyEnvironment::Development,
                $this->json['require-dev'] ?? [],
            );

            return $this;
        }

        $this->addPhpExtensionsFromPairs(
            DependencyLink::Required,
            DependencyEnvironment::Production,
            $this->lock['platform'] ?? [],
        );
        $this->addPhpExtensionsFromPairs(
            DependencyLink::Required,
            DependencyEnvironment::Production,
            $this->lock['platform-dev'] ?? [],
        );

        return $this;
    }

    /**
     * @param array<string, string> $pairs
     */
    protected function addPhpExtensionsFromPairs(
        DependencyLink $link,
        DependencyEnvironment $environment,
        array $pairs,
    ): static {
        $filter = function (string $key): bool {
            return preg_match('@^ext-[^/]+$@', $key) === 1;
        };
        $list = array_filter($pairs, $filter, \ARRAY_FILTER_USE_KEY);

        foreach ($list as $name => $versionConstraint) {
            if (array_key_exists($name, $this->dependencies)) {
                continue;
            }

            $this->dependencies[$name] = new PhpExtDependency(
                $name,
                $link,
                $environment,
                $versionConstraint,
            );
        }

        return $this;
    }

    protected function addApi(): static
    {
        if ($this->json) {
            $this->addApiFromPairs(
                DependencyLink::Required,
                DependencyEnvironment::Production,
                $this->json['require'] ?? [],
            );
            $this->addApiFromPairs(
                DependencyLink::Required,
                DependencyEnvironment::Development,
                $this->json['require-dev'] ?? [],
            );
        }

        return $this;
    }

    /**
     * @param array<string, string> $pairs
     */
    protected function addApiFromPairs(
        DependencyLink $link,
        DependencyEnvironment $environment,
        array $pairs,
    ): static {
        $filter = function (string $key): bool {
            return $key !== 'php'
                && preg_match('@^ext-[^/]+$@', $key) !== 1
                && !str_contains('/', $key);
        };
        $list = array_filter($pairs, $filter, \ARRAY_FILTER_USE_KEY);

        foreach ($list as $name => $versionConstraint) {
            if (array_key_exists($name, $this->dependencies)) {
                continue;
            }

            $this->dependencies[$name] = new ApiDependency(
                $name,
                $link,
                $environment,
                $versionConstraint,
            );
        }

        return $this;
    }

    protected function addPackage(): static
    {
        /** @var array{name: string} $lockEntry */
        foreach ($this->lock['packages'] ?? [] as $lockEntry) {
            $this->dependencies[$lockEntry['name']] = new PackageDependency(
                $lockEntry,
                DependencyLink::Required,
                DependencyEnvironment::Production,
                [],
                $this->json['require'][$lockEntry['name']] ?? null,
            );
        }

        /** @var array{name: string} $lockEntry */
        foreach ($this->lock['packages-dev'] ?? [] as $lockEntry) {
            $this->dependencies[$lockEntry['name']] = new PackageDependency(
                $lockEntry,
                DependencyLink::Required,
                DependencyEnvironment::Development,
                [],
                $this->json['require-dev'][$lockEntry['name']] ?? null,
            );
        }

        return $this;
    }
}
