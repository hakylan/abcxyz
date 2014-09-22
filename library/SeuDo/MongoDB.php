<?php

namespace SeuDo;

use Flywheel\Config\ConfigHandler;
use MongoQB\Builder;

class MongoDB {
    /** @var Builder[] */
    protected static $_conn;

    const CONFIG_KEY_LOGGING = 'logging';
    const CONFIG_KEY_SEUDO = 'seudo';

    /**
     * @param null $configKey
     * @return Builder
     * @throws \MongoQB\Exception
     */
    public static function getConnection($configKey = null) {
        $config = ConfigHandler::get('mongodb');
        if (!$configKey || !isset($config[$configKey])) {
            $configKey = $config['__default__'];
        }
        $config = $config[$configKey];
        if (!isset(self::$_conn[$configKey])) {
            $c = new Builder($config);

            self::$_conn[$configKey] = $c;
        }
        return self::$_conn[$configKey];
    }
} 