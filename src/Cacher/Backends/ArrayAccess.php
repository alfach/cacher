<?php namespace Cacher\Backends;

use ArrayAccess as ArrayAccessInterface;

class ArrayAccess extends Memory
{
    public function __construct(ArrayAccessInterface $array, $namespace = '')
    {
        $this->store = $array;
        
        parent::__construct($namespace);
    }
}
