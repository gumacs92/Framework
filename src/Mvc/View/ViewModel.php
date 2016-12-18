<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-09
 * Time: 09:59 AM
 */

namespace Framework\Mvc\View;

use Framework\Abstractions\Exceptions\ViewException;

class ViewModel
{
    private $view;
    private $path;
    private $entities = [];

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function has($key){
        return array_key_exists($key, $this->entities);
    }

    public function set($key, $value)
    {
        $this->entities[$key] = $value;
    }

    public function getReal($key)
    {
        return $this->entities[$key];
    }

    public function get($key){
        return htmlspecialchars($this->entities[$key]);
    }

    public function delete($key)
    {
        unset($this->entities[$key]);
    }

    public function setView($view)
    {
        $this->view = $view;
    }

    public function showView()
    {
        $view = $this->path . DIRECTORY_SEPARATOR . $this->view;
        if (file_exists($view)) {
            require $view;
            return;
        }
        throw new ViewException("Fatal Error: view does not exist: " . $view);
    }

    public function setAndShowView($view)
    {
        $this->setView($view);
        $this->showView();
    }

    public function setAndShowActionView()
    {
        $action_name = debug_backtrace()[1]['function'];
        $action = substr($action_name, 0, -6) . '.php';
        $this->setAndShowView($action);
    }
}