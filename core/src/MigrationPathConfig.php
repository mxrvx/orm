<?php

declare(strict_types=1);

namespace MXRVX\ORM;

use MXRVX\ORM\Tools\Packages;

class MigrationPathConfig extends EntityPathConfig
{
    protected string $namespace = '';

    public function setNameSpace(string $namespace): static
    {
        $this->namespace = $namespace;
        $this->setPaths([$namespace => $this->getEntitiesDirectory()]);
        return $this;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @psalm-assert-if-true array{'default': string} $this->paths
     */
    public function isConfigured(): bool
    {
        return !empty($this->namespace) && \count($this->paths) === 1;
    }

    public function getNameSpaceDirectory(): string
    {
        return Packages::getComponentsDirectory($this->namespace);
    }

    public function getDirectory(): string
    {
        return Packages::getMigrationsDirectory($this->namespace);
    }

    public function getEntitiesDirectory(): string
    {
        return Packages::getEntitiesDirectory($this->namespace);
    }

    public function getTable(): string
    {
        return Packages::getMigrationsTable($this->namespace);
    }
}
