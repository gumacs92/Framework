<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-08
 * Time: 11:36 PM
 */

namespace Tests\Unit\Components\Router;


use Framework\Components\Router\Route;
use Framework\Components\Router\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    /* @var Router $router */
    private $router;

    public function setUp()
    {
        $this->router = new Router();

        $this->router->addRoute(new Route('/test1',
            [
                'controller' => 'Tests\Helpers\Controllers\Test',
                'action' => 'test',
            ]));

        $this->router->addRoute(new Route('/test1/:int',
            [
                'controller' => 'Tests\Helpers\Controllers\Test',
                'action' => 'testWithInt',
                'int' => 1,
            ]));
    }

    public function testExecute(){
        $_SERVER['_uri'] = '/test1/12';
        $return = $this->router->dispatch();

        $this->assertEquals(true, $return);
    }
}
