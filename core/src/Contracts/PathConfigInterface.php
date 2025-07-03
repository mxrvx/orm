<?php

declare(strict_types=1);

namespace MXRVX\ORM\Contracts;

/**
 * @psalm-type configMetaData = array<string, string>
 */
interface PathConfigInterface
{
    public function addPath(string $namespace, string $path): void;

    public function getPath(string $namespace): ?string;

    /**
     * @return configMetaData
     */
    public function getPaths(): array;

    public function getCount(): int;

    public function getHash(): string;

    public function isEmpty(): bool;

    public function hasNamespace(string $namespace): bool;
}
