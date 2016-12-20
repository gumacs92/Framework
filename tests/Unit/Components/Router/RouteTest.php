<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-08
 * Time: 09:59 AM
 */

namespace Tests\Unit\Components\Router;

use Framework\Abstractions\Exceptions\RouteException;
use Framework\Components\Autoloader\AutoLoader;
use Framework\Components\Router\Route;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    /* @var Route $route */
    private $route;



    public function testExecute4(){
        $this->route = new Route('/test2/:int/:params', [
            'controller' => 'Tests\Helpers\Controllers\Test',
            'action' => 'testWithIntParams',
            'int' => 1,
            'params' => 2,
        ]);

        $return = $this->route->execute('/test2/12/13/14');

        $this->assertEquals(true, $return);
    }

    public function testExecute3(){
        $this->route = new Route('/test2/:params', [
            'controller' => 'Tests\Helpers\Controllers\Test',
            'action' => 'testWithParams',
            'params' => 1,
        ]);

        $return = $this->route->execute('/test2/12/13');

        $this->assertEquals(true, $return);
    }

    public function testExecute2(){
        $this->route = new Route('/test2/:int', [
            'controller' => 'Tests\Helpers\Controllers\Test',
            'action' => 'testWithInt',
            'int' => 1,
        ]);

        $return = $this->route->execute('/test2/12');

        $this->assertEquals(true, $return);
    }

    public function testExecute1(){
        $this->route = new Route('/test1', [
            'controller' => 'Tests\Helpers\Controllers\Test',
            'action' => 'test',
        ]);

        $return = $this->route->execute('/test1');

        $this->assertEquals(true, $return);
    }

    public function testMatchTemplateUri4(){
        $this->route = new Route('/show_post/#[0-9]{4}#/:params', [
            'controller' => 'index',
            'action' => 'index',
            'year' => 1,
            'params' => 2,
        ]);

        $return = $this->route->matchTemplateUri('/show_post/1992/asd/$asdflj_-');

        $result = $this->route->getUriSettings();
        $this->assertEquals(true, $return);
        $this->assertEquals('index', $result['controller']);
        $this->assertEquals('index', $result['action']);
        $this->assertEquals('1992', $result['year']);
        $this->assertEquals('asd/$asdflj_-', $result['params']);
    }

    public function testMatchTemplateUri3(){
        $this->route = new Route('/show_post/adult/:params', [
            'controller' => 'index',
            'action' => 'index',
            'params' => 1,
        ]);

        $return = $this->route->matchTemplateUri('/show_post/adult/asd/$asdflj_-');

        $result = $this->route->getUriSettings();
        $this->assertEquals(true, $return);
        $this->assertEquals('index', $result['controller']);
        $this->assertEquals('index', $result['action']);
        $this->assertEquals('asd/$asdflj_-', $result['params']);
    }

    public function testMatchTemplateUri2(){
        $this->route = new Route('/:controller/:action/:params', [
            'controller' => 1,
            'action' => 2,
            'params' => 3,
        ]);

        $return = $this->route->matchTemplateUri('/index/index/asd/$asdflj_-');

        $result = $this->route->getUriSettings();
        $this->assertEquals(true, $return);
        $this->assertEquals('index', $result['controller']);
        $this->assertEquals('index', $result['action']);
        $this->assertEquals('asd/$asdflj_-', $result['params']);
    }

    public function testMatchTemplateUri1(){
        $this->route = new Route('/:controller/:action', [
            'controller' => 1,
            'action' => 2,
        ]);

        $return = $this->route->matchTemplateUri('/index/index');

        $result = $this->route->getUriSettings();
        $this->assertEquals(true, $return);
        $this->assertEquals('index', $result['controller']);
        $this->assertEquals('index', $result['action']);
    }

    public function testValidateUriTemplate()
    {
        $uri1 = '/:namespace/:module/:controller/:action/:int/sadfdsa/#(.*)*#/:params';
        $uri_settings1 = [
            'namespace' => 1,
            'module' => 2,
            'controller' => 3,
            'action' => 4,
            'int' => 5,
            'regex' => 6,
            'params' => 7,
        ];

        Route::validateUriTemplate($uri1, $uri_settings1);

        $this->assertTrue(true);
    }

        public function testValidateUriTemplateExceptionByNoController()
        {
            $this->expectException(RouteException::class);
            $this->expectExceptionMessage("Fatal error: Invalid uri: a controller and an action is required");

            $uri1 = ':namespace/:module/:action/:int/sadfdsa/#(.*)*#/:params';
            $uri_settings1 = [
                'namespace' => 1,
                'module' => 2,
                'action' => 4,
                'int' => 5,
                'regex' => 6,
                'params' => 7,
            ];

            Route::validateUriTemplate($uri1, $uri_settings1);

            $this->assertTrue(true);
        }

    public function testValidateUriTemplateExceptionByInvalidTemplatePosition()
    {
        $this->expectException(RouteException::class);
        $this->expectExceptionMessage("Fatal error: Invalid uri: named template is at an invalid referenced position");

        $uri1 = ':namespace/:module/:controller/:action/:int/sadfdsa/#(.*)*#/:params';
        $uri_settings1 = [
            'namespace' => 1,
            'module' => 2,
            'controller' => 3,
            'action' => 5,
            'int' => 4,
            'regex' => 6,
            'params' => 7,
        ];

        Route::validateUriTemplate($uri1, $uri_settings1);

        $this->assertTrue(true);
    }

    public function testValidateUriTemplateExceptionByMalformed1()
    {
        $this->expectException(RouteException::class);
        $this->expectExceptionMessage("Fatal error: Invalid uri: template is malformed");

        $uri1 = ':namespace/:module/:controller/:actin/:int/sadfdsa/#(.*)*#/:params';
        $uri_settings1 = [
            'namespace' => 1,
            'module' => 2,
            'controller' => 3,
            'action' => 4,
            'int' => 5,
            'params' => 6,
            'regex' => 7,
        ];

        Route::validateUriTemplate($uri1, $uri_settings1);
    }

    public function testValidateUriTemplateExceptionByMalformed2()
    {
        $this->expectException(RouteException::class);
        $this->expectExceptionMessage("Fatal error: Invalid uri: template is malformed");

        $uri1 = ':namespace/:module/:controller/:action/:int/sad+fdsa/#(.*)*#/:params';
        $uri_settings1 = [
            'namespace' => 1,
            'module' => 2,
            'controller' => 3,
            'action' => 4,
            'int' => 5,
            'params' => 6,
            'regex' => 7,
        ];

        Route::validateUriTemplate($uri1, $uri_settings1);
    }

    public function testValidateUriTemplateExceptionByReferencedOnce()
    {
        $this->expectException(RouteException::class);
        $this->expectExceptionMessage("Fatal error: Invalid uri: a template position can only be referenced once");

        $uri1 = ':namespace/:module/:controller/:action/:int/sadfdsa/#(.*)*#/:params';
        $uri_settings1 = [
            'namespace' => 1,
            'module' => 2,
            'controller' => 3,
            'action' => 4,
            'int' => 4,
            'params' => 6,
            'regex' => 7,
        ];

        Route::validateUriTemplate($uri1, $uri_settings1);
    }

    public function testValidateUriTemplateExceptionByReference()
    {
        $this->expectException(RouteException::class);
        $this->expectExceptionMessage("Fatal error: Invalid uri: not every template in the uri is referenced");

        $uri1 = ':namespace/:module/:controller/:action/:int/sadfdsa/#(.*)*#/:params';
        $uri_settings1 = [
            'namespace' => 1,
            'module' => 2,
            'controller' => 3,
            'action' => 4,
            'params' => 6,
            'regex' => 7,
        ];

        Route::validateUriTemplate($uri1, $uri_settings1);
    }

    public function testValidateUriTemplateExceptionByParams()
    {
        $this->expectException(RouteException::class);
        $this->expectExceptionMessage("Fatal error: Invalid uri: :params is required to be the last in the uri");

        $uri1 = ':namespace/:module/:controller/:action/:params/:int/sadfdsa/#(.*)*#';
        $uri_settings1 = [
            'namespace' => 1,
            'module' => 2,
            'controller' => 3,
            'action' => 4,
            'params' => 5,
            'int' => 6,
            'regex' => 7,
        ];

        Route::validateUriTemplate($uri1, $uri_settings1);
    }
}
