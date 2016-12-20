<?php

namespace Framework\Components\Autoloader;

use DirectoryIterator;
use Framework\Abstractions\Interfaces\IComponent;

/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-11-04
 * Time: 04:34 PM
 */
require_once __DIR__ .
    DIRECTORY_SEPARATOR . '..' .
    DIRECTORY_SEPARATOR . '..' .
    DIRECTORY_SEPARATOR . 'Abstractions' .
    DIRECTORY_SEPARATOR . 'Interfaces' .
    DIRECTORY_SEPARATOR . 'IComponent.php';

class AutoLoader implements IComponent
{
    static private $autoloader;

    private $registeredPaths = array();
    private $classNames = array();

    private $namespacePrefix;
    private $namespaceUpperCase;

    private function __construct($namespaceUpperCase = true, $namespacePrefix = '')
    {
        if (!defined('DS')) {
            define("DS", DIRECTORY_SEPARATOR);
        }

        $this->namespacePrefix = empty($namespacePrefix) ? $_SERVER['DOCUMENT_ROOT'] : $namespacePrefix;
        $this->namespaceUpperCase = $namespaceUpperCase;
    }

    /**
     * @return mixed
     */
    public static function getAutoloader($namespaceUpperCase = true, $namespacePrefix = '')
    {
        if (is_null(self::$autoloader)) {
            self::$autoloader = new AutoLoader($namespaceUpperCase, $namespacePrefix);
        }
        return self::$autoloader;
    }

    /**
     * Store the filename (sans extension) & full path of all ".php" files found
     */
    public function registerDirectory($dirName, $namespace)
    {
        $namespace_parts = explode('\\', $namespace);
        $namespace_name = $this->setCase($namespace_parts[sizeof($namespace_parts) - 1]);

        $di = new DirectoryIterator($dirName);
        /* @var DirectoryIterator $file */
        foreach ($di as $file) {

            if ($file->isDir() && !$file->isLink() && !$file->isDot()) {
                // recurse into directories generic than a few special ones
                $dir_parts = explode(DS, rtrim($file->getRealPath(), DS));
                $dir_name = $this->setCase($dir_parts[sizeof($dir_parts) - 1]);

                if ($namespace_name === $dir_name) {
                    self::registerDirectory($file->getRealPath(), implode('\\', $namespace_parts));
                } else {
                    self::registerDirectory($file->getRealPath(), implode('\\', $namespace_parts) . '\\' . $dir_name);
                }
            } elseif (substr($file->getFilename(), -4) === '.php') {
                $className = $namespace . '\\' . substr($file->getFilename(), 0, -4);
                AutoLoader::addClass($className, $file->getRealPath());
            }
        }
    }


    public function addDirectory($dirname, $prefix)
    {
        $this->registeredPaths[] = ['dir' => $dirname, 'prefix' => $prefix];
    }

    /**
     * @return array
     */
    public function getClassNames()
    {
        return $this->classNames;
    }

    public function addClass($className, $fileName)
    {
        $this->classNames[$className] = $fileName;
    }

    public function loadClass($className)
    {
        if (isset($this->classNames[$className])) {
            require_once $this->classNames[$className];
        }
    }

    public function setCase($name)
    {
        if ($this->namespaceUpperCase) {
            return ucfirst($name);
        } else {
            return lcfirst($name);
        }
    }

    public function init()
    {
        foreach ($this->registeredPaths as $path) {
            AutoLoader::registerDirectory($path['dir'], $path['prefix']);
        }
    }

    public function dispatch()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }
}

