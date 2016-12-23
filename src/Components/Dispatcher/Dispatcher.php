<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-16
 * Time: 07:09 AM
 */

namespace Framework\Components\Router;


use Framework\Abstractions\Interfaces\IDispatcher;
use Framework\Components\AbstractComponent;

class Dispatcher extends AbstractComponent implements IDispatcher
{
    private $namespace;
    private $module;
    private $controller;
    private $action;
    private $int;
    private $params;

    public function init()
    {
        $reflection = new \ReflectionClass($this);

        $props = $reflection->getProperties();

        foreach ($props as $name => $value) {
            $this->$name = '';
        }
    }

    public function start()
    {

    }

    public function getActionName(){
        return $this->action . 'Action';
    }

    public function getControllerName(){
        return (empty($this->namespace) ? '' : $this->namespace . '\\')
            . (empty($this->module) ? '' : $this->module . '\\')
            . $this->controller . 'Controller';
    }

    public function getParams(){
        $arguments = [];
        if(!empty($this->int))
        $arguments[] = $this->int;
        if(!empty($params)){
            foreach ($this->params as $param){
                $arguments[] = $param;
            }
        }

        return $arguments;
    }

    public function dispatch($settings = [])
    {
        foreach ($settings as $k => $v) {
            $this->$k = $v;
        }

        $this->controllerLifeCycle($this->getControllerName());
    }

    public function controllerLifeCycle($controller_name)
    {
        $finished = false;

        //TODO beforeloop event
        while(!$finished){
            //TODO loop event

            if(class_exists($controller_name)){
                /* @var \Framework\Mvc\Controller\Controller $controller */
                $controller = new $controller_name();
                $controller->setDispatcher($this);

                $action = $this->getActionName();
                if(method_exists($controller, $action)){
                    $controller->$action
                }

                call

            }
            // Execute the action
            call_user_func_array(
                [
                    $controller,
                    $actionName . "Action"
                ],
                $params
            );
        }

        //TODO afterloop event
    }

    public function forward();
}