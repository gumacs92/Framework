<?php

/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-11-19
 * Time: 01:11 PM
 */

namespace Framework\Components\Router;
use Framework\Abstractions\Interfaces\IComponent;

class Router implements IComponent
{
    private $routes = [];
    /* @var Route $notFound */
    private $notFound;

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


    public function dispatch($uri = '')
    {
        /* @var Route $route */
        foreach ($this->routes as $route){
            $return = $route->execute($uri);
            if($return){
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
        // TODO: Implement init() method.
    }
}