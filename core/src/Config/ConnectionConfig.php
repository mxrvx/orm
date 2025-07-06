<?php

declare(strict_types=1);

namespace MXRVX\ORM\Config;

use Cycle\Database\Config\MySQL\TcpConnectionConfig;
use Cycle\Database\Config\MySQLDriverConfig;
use MXRVX\ORM\Driver\MySQL\MySQLDriver;

class ConnectionConfig
{
    /** @var array<int, int|string|bool> */
    protected array $connectionDefaultOptions = [
        \PDO::ATTR_CASE               => \PDO::CASE_NATURAL,
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        // TODO Should be moved into common driver settings.
        \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES "UTF8"',
        \PDO::ATTR_STRINGIFY_FETCHES  => false,
    ];

    /** @var array<string, bool> */
    protected array $driverDefaultOptions = [
        'withDatetimeMicroseconds' => true,
        'logInterpolatedQueries' => true,
        'logQueryParameters' => false,
    ];

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
        /** @var array<int, int|string|bool> */
        private array  $connectionOptions = [],
        /** @var array<string, bool> */
        private array  $driverOptions = [],
    ) {
        $this->port = \max(1, (int) $port);
        $this->connectionOptions = $connectionOptions + $this->connectionDefaultOptions;
        $this->driverOptions = $driverOptions + $this->driverDefaultOptions;
    }

    public function toDriverConfig(): MySQLDriverConfig
    {
        /** @psalm-suppress InvalidArgument */
        return new MySQLDriverConfig(
            driver: MySQLDriver::class,
            connection: new TcpConnectionConfig(
                host: $this->host,
                port: $this->port,
                user: $this->user,
                password: $this->password,
                database: $this->database,
                charset: $this->charset,
                options: $this->connectionOptions,
            ),
            queryCache: true,
            options: $this->driverOptions,
        );
    }

    public function toDatabaseConfig(string $name): array
    {
        return [
            'connection' => $name,
            'prefix' => $this->prefix,
        ];
    }
}
