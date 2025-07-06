<?php

declare(strict_types=1);

namespace MXRVX\ORM\Tools;

class Packages
{
    public static function getComponentsDirectory(string $namespace): string
    {
        return \sprintf('%s/components/%s/', \rtrim(MODX_CORE_PATH, '/'), $namespace);
    }

    public static function getMigrationsDirectory(string $namespace): string
    {
        return \sprintf('%s/migrations/', \rtrim(self::getComponentsDirectory($namespace), '/'));
    }

    public static function getEntitiesDirectory(string $namespace): string
    {
        return \sprintf('%s/src/Entities/', \rtrim(self::getComponentsDirectory($namespace), '/'));
    }

    public static function getCacheDirectory(string $namespace): string
    {
        return \sprintf('%s/cache/%s', \rtrim(MODX_CORE_PATH, '/'), $namespace);
    }

    public static function getMigrationsTable(string $namespace): string
    {
        return \sprintf('%s_migrations', \str_replace('-', '_', $namespace));
    }

    public static function getVendorDirectory(string $namespace): string
    {
        return \sprintf('%s/vendor/%s/', \rtrim(MODX_CORE_PATH, '/'), (string) \preg_replace('/-/', '/', $namespace, 1));
    }

    public static function getVendorEntitiesDirectory(string $namespace): string
    {
        return \sprintf('%s/core/src/Entities/', \rtrim(self::getVendorDirectory($namespace), '/'));
    }
}
