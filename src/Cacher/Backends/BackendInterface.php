<?php namespace Cacher\Backends;

interface BackendInterface {
    
    public function hits();
    
    public function misses();
    
    public function get($key);
    
    public function put($key, $value, $time = null);
    
    public function delete($key);
    
    public function keep($key, $time = null);
    
    public function key($key);
    
}