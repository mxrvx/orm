<?php

declare(strict_types=1);

namespace MXRVX\ORM\Tools;

class Packages
{
    public static function getNameSpaceDirectory(string $namespace): string
    {
        return \sprintf('%s/components/%s/', \rtrim(MODX_CORE_PATH, '/'), $namespace);
    }

    public static function getMigrationsDirectory(string $namespace): string
    {
        return \sprintf('%s/migrations/', \rtrim(self::getNameSpaceDirectory($namespace), '/'));
    }

    public static function getEntitiesDirectory(string $namespace): string
    {
        return \sprintf('%s/src/Entities/', \rtrim(self::getNameSpaceDirectory($namespace), '/'));
    }

    public static function getCacheDirectory(string $namespace): string
    {
        return \sprintf('%s/cache/%s', \rtrim(MODX_CORE_PATH, '/'), $namespace);
    }

    public static function getMigrationsTable(string $namespace): string
    {
        return \sprintf('%s_migrations', \str_replace('-', '_', $namespace));
    }
}
