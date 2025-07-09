<?php

declare(strict_types=1);

namespace MXRVX\ORM;

use Monolog\Level;
use MXRVX\Schema\System\Settings;
use MXRVX\Schema\System\Settings\SchemaConfig;
use MXRVX\Schema\System\Settings\SchemaConfigInterface;

class Config extends SchemaConfig
{
    public static function make(array $config): SchemaConfigInterface
    {
        $schema = Settings\Schema::define(App::getNameSpaceSlug())
            ->withSettings(
                [
                    Settings\Setting::define(
                        key: 'schema_cache',
                        value: true,
                        xtype: 'combo-boolean',
                        typecast: Settings\TypeCaster::BOOLEAN,
                    ),
                    Settings\Setting::define(
                        key: 'sql_log_active',
                        value: false,
                        xtype: 'combo-boolean',
                        typecast: Settings\TypeCaster::BOOLEAN,
                    ),
                    Settings\Setting::define(
                        key: 'sql_log_level',
                        value: Level::Debug->value,
                        xtype: 'numberfield',
                        typecast: Settings\TypeCaster::INTEGER,
                    ),
                    Settings\Setting::define(
                        key: 'sql_log_path',
                        value: App::getLogDirectory(),
                        xtype: 'textfield',
                        typecast: Settings\TypeCaster::STRING,
                    ),
                ],
            );
        return Config::define($schema)->withConfig($config);
    }
}
