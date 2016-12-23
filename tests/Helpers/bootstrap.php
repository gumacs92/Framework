<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-08
 * Time: 10:29 PM
 */

use Framework\Components\Autoloader\AutoLoader;

require $_SERVER['DOCUMENT_ROOT'] .
    DIRECTORY_SEPARATOR . 'src' .
    DIRECTORY_SEPARATOR . 'Components' .
    DIRECTORY_SEPARATOR . 'Autoloader' .
    DIRECTORY_SEPARATOR . 'AutoLoader.php';

$autoloader = AutoLoader::getAutoloader();

define('SRC', getcwd() . DS . 'src' . DS);
define('TESTS', getcwd() . DS . 'tests' . DS);

$autoloader->addDirectory(SRC, 'Framework');
$autoloader->addDirectory(TESTS, 'Tests');
$autoloader->init();
$autoloader->start();
