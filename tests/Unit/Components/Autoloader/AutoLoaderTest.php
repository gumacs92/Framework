<?php
namespace Tests\Unit\Components\Autoloader;

use Framework\Components\Autoloader\AutoLoader;
use Framework\Mvc\View\ViewModel;

/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-06
 * Time: 12:31 PM
 */
class AutoLoaderTest extends \PHPUnit_Framework_TestCase
{
    /* @var AutoLoader $autoloader */
    private $autoloader;

    public function setUp()
    {
        if(!defined("TESTDIR1")){
            define("TESTDIR1", getcwd() . DS . 'src' . DS . 'Components' . DS);
            define("TESTDIR2", getcwd() . DS . 'src' . DS . 'Abstractions' . DS);
            define("TESTDIR3", getcwd() . DS . 'src' . DS . 'Core' . DS);
        }
        require_once $_SERVER['DOCUMENT_ROOT'] . DS . 'src' . DS . 'Abstractions' . DS . 'Interfaces' . DS . "IComponent.php";
        require_once $_SERVER['DOCUMENT_ROOT'] . DS . 'src' . DS . 'Components' . DS . 'Autoloader' . DS . "AutoLoader.php";

        $this->autoloader = AutoLoader::getAutoloader();
    }

    public function testInit()
    {
        $this->autoloader->init();

        $names = $this->autoloader->getClassNames();

        $this->assertEquals(TESTDIR3 . "AuthService.php", $names['Framework\Core\AuthService']);
        $this->assertEquals(TESTDIR1 . 'Autoloader' . DS . "AutoLoader.php", $names['Framework\Components\Autoloader\AutoLoader']);

        $this->assertEquals(TESTDIR2 . 'Interfaces' . DS . "IAuth.php", $names['Framework\Abstractions\Interfaces\IAuth']);
        $this->assertEquals(TESTDIR2 . 'Interfaces' . DS . "IComponent.php", $names['Framework\Abstractions\Interfaces\IComponent']);
    }

    public function testDispatch(){
        $this->autoloader->dispatch();

//        $testClass1 = new \framework\Core\ComponentHandler();
        $testClass2 = new ViewModel('');
    }
}
