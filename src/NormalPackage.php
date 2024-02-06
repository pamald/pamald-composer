<?php

declare(strict_types = 1);

namespace Pamald\PamaldComposer;

use Pamald\Pamald\PackageInterface;
use Pamald\Pamald\PackageJsonSerializerTrait;
use Sweetchuck\Utils\VersionNumber;

class NormalPackage implements PackageInterface
{
    use PackageJsonSerializerTrait;

    protected ?VersionNumber $version = null;

    /**
     * @phpstan-param array<string, mixed> $lockEntry
     * @phpstan-param array<string, mixed> $patches
     */
    public function __construct(
        protected array $lockEntry,
        protected ?string $typeOfRelationship = null,
        protected array $patches = [],
        protected ?string $versionConstraint = null,
    ) {
        if (!empty($this->lockEntry['version'])
            && VersionNumber::isValid($this->lockEntry['version'])
        ) {
            $this->version = VersionNumber::createFromString($this->lockEntry['version']);
        }
    }

    public function name(): string
    {
        return $this->lockEntry['name'];
    }

    public function type(): ?string
    {
        return null;
    }

    public function versionString(): ?string
    {
        return $this->lockEntry['version'];
    }

    public function version(): ?VersionNumber
    {
        return $this->version;
    }

    public function typeOfRelationship(): ?string
    {
        return $this->typeOfRelationship;
    }

    public function isDirectDependency(): ?bool
    {
        return $this->versionConstraint !== null;
    }

    public function homepage(): ?string
    {
        return null;
    }

    public function vcsInfo(): ?array
    {
        return null;
    }

    public function issueTracker(): ?array
    {
        return null;
    }
}
