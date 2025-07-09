<?php

declare(strict_types=1);

namespace MXRVX\ORM;

use DI\Container;
use MXRVX\ORM\Tools\Files;
use MXRVX\ORM\Tools\Packages;
use MXRVX\Schema\System\Settings\SchemaConfigInterface;

class App
{
    public SchemaConfigInterface $config;

    public function __construct(public \modX $modx)
    {
        $this->config = Config::make($modx->config);
    }

    public static function getLogDirectory(): string
    {
        return Packages::getLogDirectory(self::getNameSpaceSlug());
    }

    public static function getCacheDirectory(): string
    {
        return Packages::getCacheDirectory(self::getNameSpaceSlug());
    }

    public static function initCacheDirectory(): void
    {
        Files::createDirectory(self::getCacheDirectory());
    }

    public static function clearCache(): void
    {
        $directory = self::getCacheDirectory();
        if (Files::isDirectory($directory)) {
            Files::deleteDirectory($directory, true);
        } else {
            Files::createDirectory($directory);
        }
    }

    public static function injectDependencies(\modX $modx): void
    {
        self::injectBindings($modx);
        self::injectEvents($modx);
    }

    public static function getNameSpace(): string
    {
        return __NAMESPACE__;
    }

    public static function getNameSpaceSlug(): string
    {
        return \strtolower(\str_replace('\\', '-', __NAMESPACE__));
    }

    public static function getNameSpacePascalCase(): string
    {
        return \str_replace(' ', '', \ucwords(\strtolower(\str_replace('\\', ' ', __NAMESPACE__))));
    }

    private static function injectBindings(\modX $modx): void
    {
        /** @var Container $container */
        $container = \MXRVX\Autoloader\App::container();

        $registrators = [
            Registrators\RegisterConfigs::class,
            Registrators\RegisterDatabase::class,
            Registrators\RegisterGenerators::class,
            Registrators\RegisterSchema::class,
            Registrators\RegisterORM::class,
        ];

        foreach ($registrators as $registrator) {
            (new $registrator())($container);
        }
    }

    private static function injectEvents(\modX $modx): void
    {
        /** @psalm-suppress UnsupportedPropertyReferenceUsage */
        if (\property_exists($modx, 'eventMap')) {
            /** @var array<string, array<array-key,string>> $eventMap */
            $eventMap = &$modx->eventMap;
        } else {
            return;
        }

        /** @psalm-suppress UnsupportedPropertyReferenceUsage */
        if (\property_exists($modx, 'pluginCache')) {
            /** @var array<array-key,array<string,mixed>> $pluginCache */
            $pluginCache = &$modx->pluginCache;
        } else {
            return;
        }

        $postfix = 'Event.php';
        /** @var \Iterator<\SplFileInfo> $files */
        $files = new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(\dirname(__DIR__, 1) . '/elements/events/'),
            ),
            \sprintf('/^.+%s$/', $postfix),
        );

        foreach ($files as $file) {
            $eventPath = (string) $file->getRealPath();
            $eventName = \str_replace($postfix, '', $file->getFilename());
            $pluginName = \sprintf('%sPlugin%s', self::getNameSpacePascalCase(), $eventName);
            if (!$pluginCode = \file_get_contents($eventPath)) {
                continue;
            }
            $pluginCode = \preg_replace('#^<\?php#', '', $pluginCode);

            if (empty($eventMap[$eventName])) {
                $eventMap[$eventName] = [];
            }
            if (\is_array($eventMap[$eventName])) {
                $eventMap[$eventName][$pluginName] = $pluginName;
            }

            if (!isset($pluginCache[$pluginName])) {
                $plugin = $modx->newObject(\modPlugin::class);
                if ($plugin instanceof \modPlugin) {
                    $plugin->fromArray([
                        'name' => $pluginName,
                        'description' => '',
                        'plugincode' => $pluginCode,
                        'static' => 0,
                        'static_file' => $eventPath,
                    ], '', true, true);
                    $pluginCache[$pluginName] = $plugin->toArray();
                }
            }
        }
    }
}
