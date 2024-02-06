<?php

declare(strict_types = 1);

namespace Pamald\PamaldComposer;

use Pamald\Pamald\PackageCollectorInterface;

/**
 * @todo Support for "alias" or "meta" package type.
 * For example: "composer-plugin-api".
 */
class PackageCollector implements PackageCollectorInterface
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
     * @var array<string, \Pamald\Pamald\PackageInterface>
     */
    protected array $packages;

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
        $this->packages = [];

        $this
            ->addPhpCore()
            ->addPhpExtensions()
            ->addNormal();

        return $this->packages;
    }

    protected function addPhpCore(): static
    {
        if (!empty($this->json['require']['php'])) {
            $this->packages['php'] = new PhpCorePackage(
                'prod',
                $this->json['require']['php'],
            );

            return $this;
        }

        if (!empty($this->lock['platform']['php'])) {
            $this->packages['php'] = new PhpCorePackage(
                'prod',
                $this->lock['platform']['php'],
            );
        }

        return $this;
    }

    protected function addPhpExtensions(): static
    {
        if ($this->json) {
            $this->addPhpExtensionsFromPairs('prod', $this->json['require'] ?? []);
            $this->addPhpExtensionsFromPairs('dev', $this->json['require-dev'] ?? []);

            return $this;
        }

        $this->addPhpExtensionsFromPairs('prod', $this->lock['platform'] ?? []);
        $this->addPhpExtensionsFromPairs('dev', $this->lock['platform-dev'] ?? []);

        return $this;
    }

    /**
     * @param array<string, string> $pairs
     */
    protected function addPhpExtensionsFromPairs(string $typeOfRelationship, array $pairs): static
    {
        $filter = function (string $key): bool {
            return preg_match('@^ext-[^/]+$@', $key) === 1;
        };
        $list = array_filter($pairs, $filter, \ARRAY_FILTER_USE_KEY);

        foreach ($list as $name => $versionConstraint) {
            if (array_key_exists($name, $this->packages)) {
                continue;
            }

            $this->packages[$name] = new PhpExtPackage(
                $name,
                $typeOfRelationship,
                $versionConstraint,
            );
        }

        return $this;
    }

    protected function addNormal(): static
    {
        /** @var array{name: string} $lockEntry */
        foreach ($this->lock['packages'] ?? [] as $lockEntry) {
            $this->packages[$lockEntry['name']] = new NormalPackage(
                $lockEntry,
                'prod',
                [],
                $this->json['require'][$lockEntry['name']] ?? null,
            );
        }

        /** @var array{name: string} $lockEntry */
        foreach ($this->lock['packages-dev'] ?? [] as $lockEntry) {
            $this->packages[$lockEntry['name']] = new NormalPackage(
                $lockEntry,
                'dev',
                [],
                $this->json['require-dev'][$lockEntry['name']] ?? null,
            );
        }

        return $this;
    }
}
