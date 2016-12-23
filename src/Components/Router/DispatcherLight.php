<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-22
 * Time: 02:19 PM
 */

namespace Framework\Components\Router;


use Framework\Abstractions\Interfaces\IDispatcher;

class DispatcherLight implements IDispatcher
{

    public function dispatch($settings = [])
    {
        $controller_name =
            (isset($settings['namespace']) ? $settings['namespace'] . '\\' : '') .
            (isset($settings['module']) ? $settings['module'] . '\\' : '') .
            $settings['controller'] . 'Controller';
        $action_name = $settings['action'] . 'Action';
        $int = isset($settings['int']) ? $settings['int'] : null;
        $params = isset($settings['params']) ? explode('/', $settings['params']) : null;

        $arguments[] = $int;
        if(!empty($params)){
            foreach ($params as $param){
                $arguments[] = $param;
            }
        }

        $controller = new $controller_name();
        $controller->$action_name(...$arguments);
    }
}