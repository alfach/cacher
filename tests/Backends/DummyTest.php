<?php

class DummyTest extends PHPUnit_Framework_TestCase
{
    public function testGetReturnsNull()
    {
        $dummy = new Cacher\Backends\Dummy();
        
        $this->assertNull($dummy->get('test'));
    }
    
    public function testPutReturnsNull()
    {
        $dummy = new Cacher\Backends\Dummy();
        
        $this->assertNull($dummy->put('test', 1234));
    }
    
    public function testDeleteReturnsNull()
    {        
        $dummy = new Cacher\Backends\Dummy();
        
        $this->assertNull($dummy->delete('test'));
    }
    
    public function testKeepReturnsNull()
    {        
        $dummy = new Cacher\Backends\Dummy();
        
        $this->assertNull($dummy->keep('test', 100));
    }
}