<?php

declare(strict_types=1);

namespace MXRVX\ORM\Config;

/**
 * @psalm-type MetaDataConfig = array{
 * host: non-empty-string,
 * username: non-empty-string,
 * password: non-empty-string,
 * dbname: non-empty-string,
 * charset: non-empty-string,
 * table_prefix: string,
 * }
 */
class ModxConnectionConfig extends ConnectionConfig
{
    public function __construct(protected \modX $modx)
    {
        $config = [];
        $connection = $modx->getConnection();
        if ($connection instanceof \xPDOConnection) {
            /** @var MetaDataConfig $config */
            $config = $connection->config;
        }

        parent::__construct(
            host: $config['host'] ?? 'localhost',
            port: 3306,
            user: $config['username'] ?? 'user',
            password: $config['password'] ?? 'password',
            database: $config['dbname'] ?? 'database',
            charset: $config['charset'] ?? 'utf8mb4',
            prefix: $config['table_prefix'] ?? '',
        );
    }
}
