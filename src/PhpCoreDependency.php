<?php

declare(strict_types = 1);

namespace Pamald\PamaldComposer;

use Pamald\Pamald\DependencyInterface;
use Pamald\Pamald\DependencyJsonSerializerTrait;
use Pamald\Pamald\DependencyType;
use Pamald\Pamald\DependencyLink;
use Pamald\Pamald\DependencyEnvironment;
use Sweetchuck\Utils\VersionNumber;

class PhpCoreDependency implements DependencyInterface
{
    use DependencyJsonSerializerTrait;

    protected DependencyType $type = DependencyType::Platform;

    protected ?VersionNumber $version = null;

    public function __construct(
        protected ?DependencyLink $link = null,
        protected ?DependencyEnvironment $environment = null,
        protected ?string $versionConstraint = null,
    ) {
    }

    public function name(): string
    {
        return 'php';
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
        return $this->versionConstraint;
    }

    public function version(): ?VersionNumber
    {
        return $this->version;
    }

    public function isDirectDependency(): ?bool
    {
        return true;
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
