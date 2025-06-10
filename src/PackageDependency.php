<?php

declare(strict_types = 1);

namespace Pamald\PamaldComposer;

use Pamald\Pamald\DependencyEnvironment;
use Pamald\Pamald\DependencyInterface;
use Pamald\Pamald\DependencyJsonSerializerTrait;
use Pamald\Pamald\DependencyLink;
use Pamald\Pamald\DependencyType;
use Sweetchuck\Utils\VersionNumber;

class PackageDependency implements DependencyInterface
{
    use DependencyJsonSerializerTrait;

    protected ?VersionNumber $version = null;

    protected DependencyType $type = DependencyType::Package;

    /**
     * @phpstan-param array<string, mixed> $lockEntry
     * @phpstan-param array<string, mixed> $patches
     */
    public function __construct(
        protected array $lockEntry,
        protected ?DependencyLink $link = null,
        protected ?DependencyEnvironment $environment = null,
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

    public function type(): ?DependencyType
    {
        return $this->type;
    }

    public function link(): ?DependencyLink
    {
        return $this->link;
    }

    public function environment(): ?DependencyEnvironment
    {
        return $this->environment;
    }

    public function versionString(): ?string
    {
        return $this->lockEntry['version'];
    }

    public function version(): ?VersionNumber
    {
        return $this->version;
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
