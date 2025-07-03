<?php

declare(strict_types=1);

namespace MXRVX\ORM\Config;

use Cycle\Database\Config\MySQL\TcpConnectionConfig;
use Cycle\Database\Config\MySQLDriverConfig;
use MXRVX\ORM\Driver\MySQL\MySQLDriver;

class ConnectionConfig
{
    /** @var positive-int */
    private int $port;

    public function __construct(
        /** @var non-empty-string */
        private string $host,
        int|string     $port,
        /** @var non-empty-string */
        private string $user,
        /** @var non-empty-string */
        private string $password,
        /** @var non-empty-string */
        private string $database,
        /** @var non-empty-string */
        private string $charset = 'utf8mb4',
        private string $prefix = '',
        /** @var array<int, non-empty-string> */
        private array  $options = [],
    ) {
        $this->port = \max(1, (int) $port);
    }

    public function toDriverConfig(): MySQLDriverConfig
    {
        $tcpConfig = new TcpConnectionConfig(
            host: $this->host,
            port: $this->port,
            user: $this->user,
            password: $this->password,
            database: $this->database,
            charset: $this->charset,
            options: $this->options,
        );
        return new MySQLDriverConfig(driver: MySQLDriver::class, connection: $tcpConfig, queryCache: true);
    }

    public function toDatabaseConfig(string $name): array
    {
        return [
            'connection' => $name,
            'prefix' => $this->prefix,
        ];
    }
}
