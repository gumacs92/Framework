<?php
namespace Framework\Mvc\Controller;

// Base Controller

use Framework\Mvc\View\ViewModel;
use ReflectionClass;

abstract class Controller {
    /* ViewModel $viewModel */
    protected $viewModel;

    //TODO dispatcher

    public function __construct(){
        $reflector = new ReflectionClass(get_class($this));
        $controller_path = dirname($reflector->getFileName());
        $what = 'controllers';
        $with = 'views';
        $view_path = preg_replace('#' . $what .'#', $with, $controller_path);

        $this->viewModel = new ViewModel($view_path);
    }

    abstract public function beforeAction();

    abstract public function afterAction();

    public function redirect($url){
        header("Location:$url");
        exit;
    }


}