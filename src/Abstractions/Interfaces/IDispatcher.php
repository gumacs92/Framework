<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-22
 * Time: 02:24 PM
 */

namespace Framework\Abstractions\Interfaces;


interface IDispatcher
{
    public function dispatch($settings = []);
}