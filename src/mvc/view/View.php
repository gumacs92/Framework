<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-09
 * Time: 10:02 AM
 */

namespace Framework\Mvc\View;

use Framework\Abstractions\Exceptions\ViewException;

class View
{
    private $path;
    private $view;

    /**
     * View constructor.
     * @param $path
     * @param $view
     */
    public function __construct($path, $view)
    {
        $this->path = $path;
        $this->view = $view;
    }

    public function showView(){
        $view = $this->path . DIRECTORY_SEPARATOR . $this->view;
        if (file_exists($view)) {
            require $view;
        }
        throw new ViewException("Fatal Error: view does not exist: " . $view);

    }


}