<?php

class RedisTest extends PHPUnit_Framework_TestCase
{
    public function getObject()
    {
        return new Cacher\Backends\Redis(new Predis\Client());
    }
    
    public function testGetReturnsPuttedValue()
    {
        $mem = $this->getObject();
        
        $test = 1234;
        
        $mem->put('test', $test);
        
        $this->assertEquals($test, $mem->get('test'));
    }
    
    public function testGetReturnsComplexPuttedValuesAndCountsHitsAndMisses()
    {
        $mem = $this->getObject();
        
        $test = new ArrayObject(array(
            'test' => 'data',
            'for' => 'unit tests'
        ));
        
        $mem->put('test2', $test);
        
        $this->assertEquals($test, $mem->get('test2'));
        
        
        
        $this->assertNull($mem->get('test3'));
        
        $this->assertEquals(1, $mem->hits());
        
        $this->assertEquals(1, $mem->misses());
    }
    
    public function testValueTimesOut()
    {
        $mem = $this->getObject();
        
        $test = 1234;
        
        $ttl = 1;
        
        $mem->put('test', $test, $ttl);
        
        $this->assertEquals($test, $mem->get('test'));
        
        sleep($ttl);
        
        $this->assertNull($mem->get('test'));
        
    }
    
    public function testValueIsDeleted()
    {
        $mem = $this->getObject();
        
        $test = 1234;
        
        $mem->put('test', $test);
        
        $this->assertEquals($test, $mem->get('test'));
        
        $mem->delete('test');
        
        $this->assertNull($mem->get('test'));
        
    }
    
    public function testTimeValueIsKept()
    {
        $mem = $this->getObject();
        
        $test = 1234;
        
        $ttl = 1;
        
        $mem->put('test', $test, $ttl);
        
        $mem->keep('test', $ttl * 2);
        
        sleep($ttl);
        
        $this->assertEquals($test, $mem->get('test'));
        
        sleep($ttl);
        
        $this->assertNull($mem->get('test'));
        
    }
}