<?php

declare(strict_types=1);

namespace MXRVX\ORM;

use Cycle\Database\DatabaseProviderInterface;
use Cycle\Schema;
use Cycle\ORM;
use Cycle\Schema\Renderer;
use MXRVX\ORM\Tools\Files;

/**
 * @psalm-type SchemaMetaData = array<string, array<int, mixed>>
 *
 */
class SchemaFactory
{
    protected bool $schemaCache;
    protected string $schemaHash;

    public function __construct(
        public App $app,
        protected DatabaseProviderInterface $dbal,
        protected EntityPathConfig $entityPathConfig,
        protected GeneratorsFactory $generatorsFactory,
    ) {
        $this->schemaCache = (bool) $app->config->getSetting('schema_cache')?->getBoolValue();
        $this->schemaHash = $this->schemaCache ? $entityPathConfig->getHash() : '';
    }

    public function get(): ORM\Schema
    {
        if (!$schema = $this->getFromCache()) {
            $schema = $this->generate();
        }

        return new ORM\Schema($schema);
    }

    public function isCached(): bool
    {
        return $this->schemaCache;
    }

    public function getPath(): string
    {
        return \sprintf('%s/schema.%s.php', App::getCacheDirectory(), $this->schemaHash);
    }

    public function getFromCache(): ?array
    {
        $path = $this->getPath();
        if (Files::exists($path)) {
            //$schema = include $path;
            /** @var mixed $schema */
            $schema = Files::include($path);
            return $this->validate($schema) ? $schema : null;
        }
        return null;
    }

    /**
     *
     * @psalm-assert-if-true SchemaMetaData $schema
     */
    public function validate(mixed $schema): bool
    {
        if (\is_array($schema) && !empty($schema)) {
            return true;
        }
        return false;
    }

    public function saveToCache(mixed $schema): void
    {
        App::initCacheDirectory();

        if ($this->validate($schema)) {
            $renderer = new Renderer\PhpSchemaRenderer();
            Files::write($this->getPath(), $renderer->render($schema));
        }
    }

    public function generate(): array
    {
        $registry = new Schema\Registry($this->dbal);
        $compiler = new Schema\Compiler();
        $schema = $compiler->compile($registry, $this->generatorsFactory->get());
        if ($this->isCached()) {
            $this->saveToCache($schema);
        }
        return $schema;
    }
}
