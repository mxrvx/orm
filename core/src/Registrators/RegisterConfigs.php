<?php

declare(strict_types=1);

namespace MXRVX\ORM\Registrators;

use DI\Container;
use MXRVX\ORM\Config\DatabaseConfig;
use MXRVX\ORM\Config\ModxConnectionConfig;
use MXRVX\ORM\Contracts\PathConfigInterface;
use MXRVX\ORM\EntityPathConfig;
use MXRVX\ORM\Tools\Packages;

final class RegisterConfigs
{
    public static function getModxEntitiesNameSpace(): string
    {
        return 'mxrvx-orm-modx-entities';
    }

    public static function getModxEntitiesDirectory(): string
    {
        return Packages::getEntitiesDirectory(self::getModxEntitiesNameSpace());
    }

    public function __invoke(Container $c): void
    {
        $this->registerDatabaseConfig($c);
        $this->registerEntityPathConfig($c);
    }

    private function registerDatabaseConfig(Container $c): void
    {
        $c->set(ModxConnectionConfig::class, \DI\autowire());
        $c->set(DatabaseConfig::class, static function (Container $c) {
            $dbConfig = new DatabaseConfig();
            /** @var ModxConnectionConfig $modxConfig */
            $modxConfig = $c->get(ModxConnectionConfig::class);
            $dbConfig->addConnection(DatabaseConfig::DEFAULT_DATABASE, $modxConfig);

            return $dbConfig;
        });
    }

    private function registerEntityPathConfig(Container $c): void
    {
        $c->set(PathConfigInterface::class, static function (Container $c): PathConfigInterface {
            /** @var EntityPathConfig $config */
            $config = $c->get(EntityPathConfig::class);
            return $config;
        });

        $c->set(EntityPathConfig::class, static function () {
            $manager = new EntityPathConfig();
            if (\class_exists(\MXRVX\ORM\MODX\Entities\XObject::class)) {
                $manager->addPath(self::getModxEntitiesNameSpace(), self::getModxEntitiesDirectory());
            }
            return $manager;
        });
    }
}
