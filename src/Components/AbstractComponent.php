<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-22
 * Time: 02:14 PM
 */

namespace Framework\Components;


use Framework\Abstractions\Interfaces\IComponent;


//include __DIR__ .
//    DIRECTORY_SEPARATOR . '..' .
//    DIRECTORY_SEPARATOR . 'Abstractions' .
//    DIRECTORY_SEPARATOR . 'Interfaces' .
//    DIRECTORY_SEPARATOR . 'IComponent.php';

abstract class AbstractComponent implements IComponent
{
    /* @var \Framework\Core\ComponentHandler $handler */
    protected $handler;

    abstract public function init();

    abstract public function start();

    public function addHandler($handler)
    {
        $this->handler = $handler;
    }
}