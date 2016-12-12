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
        $what = DIRECTORY_SEPARATOR . 'controller';
        $with = DIRECTORY_SEPARATOR . 'view';
        $view_path = preg_replace('#' . $what .'#', $with, $controller_path);

        $this->viewModel = new ViewModel($view_path);
    }


    public function redirect($url){
        header("Location:$url");
        exit;
    }


}