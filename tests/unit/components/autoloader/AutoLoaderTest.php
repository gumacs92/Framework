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
            define("TESTDIR1", getcwd() . DS . 'src' . DS . 'components' . DS);
            define("TESTDIR2", getcwd() . DS . 'src' . DS . 'abstractions' . DS);
            define("TESTDIR3", getcwd() . DS . 'src' . DS . 'core' . DS);
        }
        require_once $_SERVER['DOCUMENT_ROOT'] . DS . 'src' . DS . 'abstractions' . DS . 'interfaces' . DS . "IComponent.php";
        require_once $_SERVER['DOCUMENT_ROOT'] . DS . 'src' . DS . 'components' . DS . 'autoloader' . DS . "AutoLoader.php";

        $this->autoloader = AutoLoader::getAutoloader();
    }

    public function testInit()
    {
        $this->autoloader->init();

        $names = $this->autoloader->getClassNames();

        $this->assertEquals(TESTDIR1 . 'authservice' . DS . "AuthService.php", $names['Framework\Components\Authservice\AuthService']);
        $this->assertEquals(TESTDIR1 . 'autoloader' . DS . "AutoLoader.php", $names['Framework\Components\Autoloader\AutoLoader']);

        $this->assertEquals(TESTDIR2 . 'interfaces' . DS . "IAuth.php", $names['Framework\Abstractions\Interfaces\IAuth']);
        $this->assertEquals(TESTDIR2 . 'interfaces' . DS . "IComponent.php", $names['Framework\Abstractions\Interfaces\IComponent']);
    }

    public function testDispatch(){
        $this->autoloader->dispatch();

//        $testClass1 = new \Framework\Core\ComponentHandler();
        $testClass2 = new ViewModel('');
    }
}
