<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-16
 * Time: 04:54 AM
 */

namespace Framework\Core;


class Core
{
    private $handler;

    public static function init($config_dir = ''){
        self::defineConst("DS", DIRECTORY_SEPARATOR);

        if (empty($config_dir)) {
            $config_dir = getcwd() .
                DS . 'application' .
                DS . 'config' . DS;
        }

        self::defineConst("CONFIG_PATH", $config_dir);

        $config = parse_ini_file($config_dir . DS . 'config.ini', true);

        // Define path constants
        self::defineConst("ROOT", getcwd() . DS);
        self::defineConst("APP_PATH", ROOT . DS . $config['Application']['app']);
        self::defineConst("PUBLIC_PATH", ROOT . DS . $config['Application']['public']);
        self::defineConst("UPLOAD_PATH", ROOT . DS . $config['Application']['upload']);
        self::defineConst("VENDOR_PATH", ROOT . DS . $config['Application']['vendor']);
        self::defineConst("CONTROLLER_PATH", ROOT . DS . $config['Application']['controller']);
        self::defineConst("MODEL_PATH", ROOT . DS . $config['Application']['model']);
        self::defineConst("VIEW_PATH", ROOT . DS . $config['Application']['view']);
        self::defineConst("LOCALE_PATH", ROOT . DS . $config['Application']['locale']);
        self::defineConst("CONFIG_PATH", ROOT . DS . $config['Application']['config']);

        require_once VENDOR_PATH . "autoload.php";
    }

    private static function defineConst($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }
}