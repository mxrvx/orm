<?php

declare(strict_types=1);

namespace MXRVX\ORM;

use MXRVX\ORM\Contracts\PathConfigInterface;
use MXRVX\ORM\Tools\Files;

/**
 * @psalm-import-type configMetaData from PathConfigInterface
 */
class EntityPathConfig implements PathConfigInterface
{
    /**
     * @var configMetaData
     */
    protected array $paths;

    /**
     * @param configMetaData|null $paths
     */
    public function __construct(
        ?array $paths = null,
    ) {
        $this->paths = $paths ?? [];
    }

    public function addPath(string $namespace, string $path, bool $checkPath = true): void
    {
        if (empty($namespace)) {
            return;
        }

        if ($checkPath && !Files::isDirectory($path)) {
            return;
        }

        if (!isset($this->paths[$namespace])) {
            $this->paths[$namespace] = $path;
        }
    }

    public function getPath(string $namespace): ?string
    {
        return $this->paths[$namespace] ?? null;
    }

    /**
     * @return configMetaData
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    public function getCount(): int
    {
        return \count($this->paths);
    }

    public function getHash(): string
    {
        $keys = \array_keys($this->paths);
        \sort($keys);

        return (string) \crc32((string) \json_encode($keys));
    }

    public function isEmpty(): bool
    {
        return $this->getCount() === 0;
    }

    public function hasNamespace(string $namespace): bool
    {
        return isset($this->paths[$namespace]);
    }

    /**
     * @param configMetaData $paths
     */
    protected function setPaths(array $paths): void
    {
        $this->paths = $paths;
    }
}
