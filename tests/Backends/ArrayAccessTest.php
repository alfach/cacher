<?php

class ArrayAccessTest extends PHPUnit_Framework_TestCase
{
    public function testObjectIsCreatedWithArrayAccess()
    {
        new \Cacher\Backends\ArrayAccess(new \ArrayObject());
    }
}
