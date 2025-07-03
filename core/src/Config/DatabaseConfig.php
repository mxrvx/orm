<?php

declare(strict_types=1);

namespace MXRVX\ORM\Config;

use Cycle\Database\Config\DatabaseConfig as CycleDatabaseConfig;

class DatabaseConfig
{
    public const DEFAULT_DATABASE = 'modx';

    /** @var array<string, ConnectionConfig> */
    private array $connections = [];

    public function addConnection(string $name, ConnectionConfig $connection): void
    {
        $this->connections[$name] = $connection;
    }

    public function toCycleConfig(): CycleDatabaseConfig
    {
        $connections = [];
        $databases = [];

        foreach ($this->connections as $name => $conn) {
            $connections[$name] = $conn->toDriverConfig();
            $databases[$name] = $conn->toDatabaseConfig($name);
        }

        $config = [
            'default' => self::DEFAULT_DATABASE,
            'databases' => $databases,
            'connections' => $connections,
        ];

        return new CycleDatabaseConfig($config);
    }
}
