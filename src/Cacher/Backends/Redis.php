<?php namespace Cacher\Backends;

class Redis extends BackendAbstract {
    
    private $redis;
        
    public function __construct($redis, $namespace = '')
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

        if($time)
        {
            return $this->redis->setex($key, $time, serialize($value));
        }

        return $this->redis->set($key, serialize($value));
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

    public function ttl($key)
    {
        $key = $this->key($key);

        $this->redis->ttl($key);
    }
}