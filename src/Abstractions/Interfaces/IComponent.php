<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-06
 * Time: 11:07 AM
 */

namespace Framework\Abstractions\Interfaces;


interface IComponent
{
    public function init();

    public function start();

    public function addHandler($handler);
}