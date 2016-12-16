<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-06
 * Time: 11:07 AM
 */

namespace Framework\Core;


use Framework\Abstractions\Exceptions\ComponentException;
use ReflectionClass;

class ComponentHandler
{
    private $componentsHolder = [];

    public function add($component)
    {
        $reflection = new ReflectionClass($component);
        if ($reflection->implementsInterface('Framework\Abstractions\Interfaces\IComponent')) {
            $this->componentsHolder[] = $component;
        } else {
            throw new ComponentException("Fatal error: the given parameter does not implement the IComponent interface");
        }
    }

    public function start()
    {
        foreach ($this->componentsHolder as $component) {
            $component->init();
            $component->dispatch();
        }
    }

}