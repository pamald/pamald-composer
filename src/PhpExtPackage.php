<?php

declare(strict_types = 1);

namespace Pamald\PamaldComposer;

use Pamald\Pamald\PackageInterface;
use Pamald\Pamald\PackageJsonSerializerTrait;
use Sweetchuck\Utils\VersionNumber;

class PhpExtPackage implements PackageInterface
{
    use PackageJsonSerializerTrait;

    protected ?VersionNumber $version = null;

    public function __construct(
        protected string $name,
        protected ?string $typeOfRelationship = null,
        protected ?string $versionConstraint = null,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function type(): ?string
    {
        return null;
    }

    public function versionString(): ?string
    {
        return $this->versionConstraint;
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
