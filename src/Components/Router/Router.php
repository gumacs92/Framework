<?php

/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-11-19
 * Time: 01:11 PM
 */

namespace Framework\Components\Router;
use Framework\Abstractions\Interfaces\IDispatcher;
use Framework\Components\AbstractComponent;

class Router extends AbstractComponent
{
    private $routes = [];
    /* @var Route $notFound */
    private $notFound;

    /* @var IDispatcher $dispatcher */
    private $dispatcher;

    public function add($uri, $uri_settings, $uri_name = '')
    {
        $this->routes[] = new Route($uri, $uri_settings, $uri_name);
    }

    public function addRoute($route)
    {
        $this->routes[] = $route;
    }

    private function showNotFound()
    {
        $this->notFound->execute();
    }

    public function setNotFound($uri, $uri_settings)
    {
        $this->notFound = new Route($uri, $uri_settings, 'notFound');
    }


    public function start($uri = '')
    {
        /* @var Route $route */
        foreach ($this->routes as $route){
            $return = $route->execute($uri);
            if($return){
                $this->dispatcher->dispatch($return);
                return true;
            }
        }
        if(isset($this->notFound)){
            $this->showNotFound();
        }

        return false;
    }

    public function init()
    {
        if(!is_null($this->handler) && $this->handler->has('dispatcher')){
            $this->dispatcher = $this->handler->get('dispatcher');
        } else {
            $this->dispatcher = new DispatcherLight();
        }
    }
}