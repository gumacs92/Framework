<?php
namespace Framework\Mvc\Controller;

// Base Controller

use Framework\Mvc\View\ViewModel;
use ReflectionClass;

abstract class Controller {
    /* ViewModel $viewModel */
    protected $viewModel;
    protected $dispatcher;

    public function __construct(){
        $reflector = new ReflectionClass(get_class($this));
        $controller_path = dirname($reflector->getFileName());
        $what = 'controllers';
        $with = 'views';
        $view_path = preg_replace('#' . $what .'#', $with, $controller_path);

        $this->viewModel = new ViewModel($view_path);
    }

    public function setDispatcher($dispatcher){
        $this->dispatcher = $dispatcher;
    }


    public function redirect($url){
        header("Location:$url");
        exit;
    }


}