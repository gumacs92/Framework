<?php
namespace Framework\Abstractions\Interfaces;

/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-10-18
 * Time: 05:04 PM
 */
interface IAuth
{
    public function getIdUser();

    public function getAuthLevel();
}