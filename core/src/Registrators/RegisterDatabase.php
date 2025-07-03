<?php

declare(strict_types=1);

namespace MXRVX\ORM\Registrators;

use Cycle\Database\DatabaseInterface;
use Cycle\Database\DatabaseProviderInterface;
use DI\Container;
use MXRVX\ORM\Config\DatabaseConfig;
use MXRVX\ORM\DataBaseFactory;

final class RegisterDatabase
{
    public function __invoke(Container $c): void
    {
        $c->set(
            DataBaseFactory::class,
            \DI\autowire()
                ->constructorParameter('databaseConfig', \DI\get(DatabaseConfig::class)),
        );

        $c->set(DatabaseProviderInterface::class, static function (Container $c): DatabaseProviderInterface {
            /** @var DataBaseFactory $factory */
            $factory = $c->get(DataBaseFactory::class);
            return $factory->get();
        });

        $c->set(DatabaseInterface::class, static function (Container $c): DatabaseInterface {
            /** @var DatabaseProviderInterface $dbal */
            $dbal = $c->get(DatabaseProviderInterface::class);
            return $dbal->database();
        });
    }
}
