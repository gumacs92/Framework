<?php
namespace Framework\Mvc\Controller;

// Base Controller

use Framework\Mvc\View\ViewModel;

abstract class Controller {
    /* ViewModel $viewModel */
    protected $viewModel;

    //TODO dispatcher

    public function __construct(){
        $controller_path = __DIR__;
        $view_path = preg_replace('//controller/', '/view', $controller_path);

        $this->viewModel = new ViewModel($view_path);
    }


    public function redirect($url){
        header("Location:$url");
        exit;
    }


}