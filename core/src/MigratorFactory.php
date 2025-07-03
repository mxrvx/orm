<?php

declare(strict_types=1);

namespace MXRVX\ORM;

use Cycle\Database\DatabaseProviderInterface;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\FileRepository;
use Cycle\Migrations\Migrator;

class MigratorFactory
{
    /** @var bool */
    public const MIGRATION_SAFE_MODE = true;

    protected Migrator $migrator;

    public function __construct(
        protected MigrationPathConfig $migrationPathConfig,
        protected DatabaseProviderInterface $dbal,
        ?bool $safeMode = null,
    ) {
        if ($safeMode === null) {
            $safeMode = self::MIGRATION_SAFE_MODE;
        }

        if (!$migrationPathConfig->isConfigured()) {
            throw new \RuntimeException('Migration Path Config is not configured');
        }

        $config = new MigrationConfig([
            'directory' => $migrationPathConfig->getDirectory(),
            'table' => $migrationPathConfig->getTable(),
            'safe' => $safeMode,
        ]);

        $this->migrator = new Migrator(
            config: $config,
            dbal: $this->dbal,
            repository: new FileRepository($config),
        );
        $this->migrator->configure();

        if (!$this->migrator->isConfigured()) {
            throw new \RuntimeException('Migrator is not configured');
        }
    }

    public function get(): Migrator
    {
        return $this->migrator;
    }

    public function getDatabaseManager(): DatabaseProviderInterface
    {
        return $this->dbal;
    }

    public function getMigrationPathConfig(): MigrationPathConfig
    {
        return $this->migrationPathConfig;
    }
}
