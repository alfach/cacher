<?php

use Mockery as m;

class CacherTest extends PHPUnit_Framework_TestCase
{
    
    public function tearDown()
    {
        m::close();
    }
    
    public function createObject($useNext = false)
    {
        $backend = m::mock('Cacher\Backends\BackendInterface');
        
        $next = $useNext ? m::mock('Cacher') : null;
        
        $cache = new Cacher($backend, $next);
        
        return array($cache, $backend, $next);
    }

    public function testObjectCallsGet()
    {
        list($cache, $backend) = $this->createObject();
        
        $backend->shouldReceive('get')->with('test')->andReturn(1234);
        
        $result = $cache->get('test');
        
        $this->assertEquals(1234, $result);
        
    }

    public function testObjectArrayAccessWorks()
    {
        list($cache, $backend) = $this->createObject();

        $backend->shouldReceive('put')->with('test', 1234, null);

        $cache['test'] = 1234;



        $backend->shouldReceive('get')->with('test')->andReturn(1234);

        $result = $cache['test'];

        $this->assertEquals(1234, $result);

    }
    
    public function testObjectCallsPutAndReturnsPlainValueAndClosure()
    {
        list($cache, $backend) = $this->createObject();
        
        $backend->shouldReceive('put')->with('test', 1234, m::any());
        
        $result = $cache->put('test', 1234);
        
        $this->assertEquals(1234, $result);
        
        
        $backend->shouldReceive('put')->with('test', 4321, m::any());
        
        $result = $cache->put('test', function(){
            return 4321;
        });
        
        $this->assertEquals(4321, $result);
        
    }
    
    public function testObjectCallsDeleteAndReturnsNull()
    {
        list($cache, $backend) = $this->createObject();
        
        $backend->shouldReceive('delete')->with('test');
        
        $result = $cache->delete('test');
        
        $this->assertNull($result);
        
    }
    
    public function testObjectCallsKeepAndReturnsNull()
    {
        list($cache, $backend) = $this->createObject();
        
        $backend->shouldReceive('keep')->with('test', null);
        
        $result = $cache->keep('test');
        
        $this->assertNull($result);
        
        
        
        $backend->shouldReceive('keep')->with('test', 100);
        
        $result = $cache->keep('test', 100);
        
        $this->assertNull($result);
        
    }
    
    public function testRememberCallsGetAndPut()
    {
        list($cache, $backend) = $this->createObject();
        
        $backend->shouldReceive('get')->with('test');
        $backend->shouldReceive('put')->with('test', 1234, null);
        
        $result = $cache->remember('test', 1234);
        
        $this->assertEquals(1234, $result);
        
        
        
        list($cache, $backend) = $this->createObject();
        
        $backend->shouldReceive('get')->with('test');
        $backend->shouldReceive('put')->with('test', 1234, 60);
        
        $result = $cache->remember('test', 1234, 60);
        
        $this->assertEquals(1234, $result);
        
    }
    
    
    /***
     * Next delagation on get
     */
    public function testGetHitDoesNotCallNextGet()
    {
        list($cache, $backend, $next) = $this->createObject(true);
        
        $backend->shouldReceive('get')->with('test')->andReturn(1234);
        
        $result = $cache->get('test');
        
        $this->assertEquals(1234, $result);
        
    }
    
    public function testGetMissCallsNextGet()
    {
        list($cache, $backend, $next) = $this->createObject(true);
        
        $backend->shouldReceive('get')->with('test')->andReturn(null);
        
        $next->shouldReceive('get')->with('test')->andReturn(1234);
        
        $result = $cache->get('test');
        
        $this->assertEquals(1234, $result);
        
    }
    
    /***
     * Next delagation on put
     */
    public function testPutCallsNextPut()
    {
        list($cache, $backend, $next) = $this->createObject(true);
        
        $time = 100;
        
        $backend->shouldReceive('put')->with('test', 1234, $time);
        
        $next->shouldReceive('put')->with('test', 1234, $time);
        
        $result = $cache->put('test', 1234, $time);
        
        $this->assertEquals(1234, $result);
        
    }
    
    /***
     * Next delagation on delete
     */
    public function testDeleteCallsNextDelete()
    {
        list($cache, $backend, $next) = $this->createObject(true);
                
        $backend->shouldReceive('delete')->with('test');
        
        $next->shouldReceive('delete')->with('test');
        
        $cache->delete('test');
        
    }
    
    /***
     * Next delagation on delete
     */
    public function testKeepCallsNextKeep()
    {
        list($cache, $backend, $next) = $this->createObject(true);
        
        $time = 100;
        
        $backend->shouldReceive('keep')->with('test', $time);
        
        $next->shouldReceive('keep')->with('test', $time);
        
        $cache->keep('test', $time);
        
    }
}

