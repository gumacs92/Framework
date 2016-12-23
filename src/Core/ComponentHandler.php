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
IComponent
IComponent
{
    private $componentsHolder = [];

    public function add($key, $component)
    {
        $reflection = new ReflectionClass($component);
        if ($reflection->implementsInterface('Framework\Abstractions\Interfaces\IComponent')) {
            /* @var \Framework\Abstractions\Interfaces\IComponent $component */
            $component->addHandler($this);
            $this->componentsHolder[$key] = $component;
        } else {
            throw new ComponentException("Fatal error: the given parameter does not implement the IComponent interface");
        }
    }

    public function has($key){
        return array_key_exists($key, $this->componentsHolder);
    }

    public function get($key){
        if($this->has($key)){
            return $this->componentsHolder[$key];
        } else {
            return null;
        }
    }

    public function start()
    {
        foreach ($this->componentsHolder as $key => $component) {
            $component->init();
            $component->dispatch();
        }
    }

}