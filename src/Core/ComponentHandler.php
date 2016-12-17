<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-06
 * Time: 11:07 AM
 */

namespace Framework\Core;


use Framework\Abstractions\Exceptions\ComponentException;
use ReflectionClass;

class ComponentHandler
{
    private $componentsHolder = [];

    public function __construct($settings = [])
    {
        // Start session
        session_start();

        // Define path constants
        if(!defined("DS")){
            define("DS", DIRECTORY_SEPARATOR);
        }
        define("ROOT", getcwd() . DS);
        define("APP_PATH", ROOT . (array_key_exists('app_dir', $settings) ? $settings['app_dir'] : 'application') . DS);
        define("PUBLIC_PATH", ROOT . "public" . DS);
        define("UPLOAD_PATH", PUBLIC_PATH . "uploads" . DS);
        define("VENDOR_PATH", ROOT . "vendor" . DS);

        define("CONFIG_PATH", (array_key_exists('app_config_dir', $settings) ? $settings['app_config_dir'] : APP_PATH . 'config') . DS);

        define("CONTROLLER_PATH", (array_key_exists('app_controller_dir', $settings) ? $settings['app_config_dir'] : APP_PATH . "controllers") . DS);
        define("MODEL_PATH", (array_key_exists('app_model_dir', $settings) ? $settings['app_config_dir'] : APP_PATH . "models") . DS);
        define("VIEW_PATH", (array_key_exists('app_view_dir', $settings) ? $settings['app_config_dir'] : APP_PATH . "views") . DS);
        define("LOCALE_PATH", (array_key_exists('app_config_dir', $settings) ? $settings['app_config_dir'] : APP_PATH . "locale") . DS);

        (array_key_exists('load_vendor_autoload', $settings) ? require_once VENDOR_PATH . "autoload.php" : '');


        // Load configuration file
        $GLOBALS['config'] = include CONFIG_PATH . (array_key_exists('app_config_file', $settings) ? $settings['app_config_file'] : "config.php");
    }

    public function add($component)
    {
        $reflection = new ReflectionClass($component);
        if ($reflection->implementsInterface('Framework\Abstractions\Interfaces\IComponent')) {
            $this->componentsHolder[] = $component;
        } else {
            throw new ComponentException("Fatal error: the given parameter does not implement the IComponent interface");
        }
    }

    public function start()
    {
        foreach ($this->componentsHolder as $component) {
            $component->init();
            $component->dispatch();
        }
    }

}