<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-06
 * Time: 11:07 AM
 */

namespace Framework\Abstractions\Interfaces;


interface ILoader
{
    public function set($path);

    public function load($viewName);
}