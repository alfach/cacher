<?php namespace Cacher\Backends;

use Predis\Client;

class Redis extends BackendAbstract {
    
    private $redis;
        
    public function __construct(Client $redis, $namespace = '')
    {
        $this->redis = $redis;
        
        parent::__construct($namespace ? ($namespace . ':') : '');
    }
    
    public function get($key)
    {
        if($result = $this->redis->get($this->key($key)))
        {
            $this->hit();
            return unserialize($result);
        }
        
        $this->miss();
    }
    
    public function put($key, $value, $time = null)
    {        
        $key = $this->key($key);
        
        $this->redis->pipeline(function($pipe) use($key, $value, $time){
            $pipe->set($key, serialize($value));
            $time and $pipe->expire($key, $time);
        });
        
        return $value;
    }
    
    public function delete($key)
    {        
        $key = $this->key($key);
        
        $this->redis->del($key);
    }
    
    public function keep($key, $time = null)
    {        
        $key = $this->key($key);
        
        $time ? $this->redis->expire($key, $time) : $this->redis->persist($key);
    }
}