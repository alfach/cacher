<?php namespace Cacher\Backends;

class Dummy extends BackendAbstract {
        
    public function get($key)
    {
        
    }
    
    public function put($key, $value, $time = null)
    {

    }
    
    public function delete($key)
    {        
        
    }
    
    public function keep($key, $time = null)
    {        
        
    }
}