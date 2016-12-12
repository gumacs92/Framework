<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-09
 * Time: 09:59 AM
 */

namespace Framework\Mvc\View;

class ViewModel
{
    /* @var View $view */
    private $view;
    private $path;
    private $props = [];

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function set($key, $value)
    {
        $this->props[$key] = $value;
    }

    public function get($key)
    {
        return $this->props[$key];
    }

    public function delete($key)
    {
        unset($this->props[$key]);
    }

    public function setView($view)
    {
        $this->view = new View($this->path, $view);
    }

    public function showView()
    {
        $this->view->showView();
    }

    public function setAndShowView($view)
    {
        $this->setView($view);
        $this->showView();
    }

    public function setAndShowCurrentActionView()
    {
        $action_name = debug_backtrace()[1]['function'];
        $action = substr($action_name, 0, -6) . '.php';
        $this->setAndShowView($action);
    }
}