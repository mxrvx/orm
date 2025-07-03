<?php

declare(strict_types=1);

namespace MXRVX\ORM;

use Cycle\Database\DatabaseManager;
use Cycle\Database\DatabaseProviderInterface;
use MXRVX\ORM\Config\DatabaseConfig;
use MXRVX\ORM\Driver\DriverWIthModxInterface;

class DataBaseFactory
{
    protected DatabaseProviderInterface $dbal;

    public function __construct(
        public App $app,
        protected DatabaseConfig $databaseConfig,
    ) {
        $logActive = $app->config->getSetting('sql_log_active')?->getBoolValue();
        $logLevel = $app->config->getSetting('sql_log_level')?->getIntegerValue() ?? 100;

        $dbal = new DatabaseManager($databaseConfig->toCycleConfig());
        foreach ($dbal->getDrivers() as $driver) {
            if ($driver instanceof DriverWIthModxInterface) {
                /** @psalm-suppress DeprecatedMethod */
                $driver->setModx($app->modx);
            }
            if ($logActive) {
                $driver->setLogger((new LoggerFactory(\sprintf('%s.sql.log', $driver->getName()), $logLevel))->get());
            }
        }

        $this->dbal = $dbal;
    }

    public function get(): DatabaseProviderInterface
    {
        return $this->dbal;
    }
}
