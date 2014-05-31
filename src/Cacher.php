<?php

use Cacher\Backends\BackendInterface;

class Cacher implements \ArrayAccess {
    
    protected $backend;
    
    protected $next;
    
    public function __construct(BackendInterface $backend, Cacher $next = null)
    {
        $this->backend = $backend;
        
        $this->next = $next;
    }

    public function get($key)
    {
        $result = $this->backend->get($key);

        if(is_null($result) && $this->next)
        {
            if(!is_null($result = $this->next->get($key)))
            {
                $this->backend->put($key, $result, $this->next->ttl($key));
            }
        }

        return $result;
    }
    
    public function put($key, $value, $time = null)
    {
        $value instanceof Closure and $value = $value();

        $this->backend->put($key, $value, $time);
        
        if($this->next)
        {
            $this->next->put($key, $value, $time);
        }
        
        return $value;
    }
    
    public function delete($key)
    {                
        $this->backend->delete($key);
        
        if($this->next)
        {
            $this->next->delete($key);
        }
    }
    
    public function keep($key, $time = null)
    {    
        $this->backend->keep($key, $time);
        
        if($this->next)
        {
            $this->next->keep($key, $time);
        }
    }
    
    public function remember($key, $value, $time = null)
    {
        return $this->get($key) ?: $this->put($key, $value, $time);
    }

    public function ttl($key)
    {
        return $this->backend->ttl($key);
    }
    
    public function hits()
    {
        $hits = $this->backend->hits();
        
        if($this->next)
        {
            $hits += $this->next->hits();
        }
        
        return $hits;
    }
    
    public function misses()
    {
        $misses = $this->backend->misses();
        
        if($this->next)
        {
            $misses += $this->next->misses();
        }
        
        return $misses;
    }
    
    public function key($key)
    {
        return $this->backend->key($key);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function offsetSet($key, $value)
    {
        $this->put($key, $value);
    }

    /*
     *
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /*
     *
     */
    public function offsetExists($key)
    {
        return (bool)$this->get($key);
    }

    /**
     * @param mixed $key
     */
    public function offsetUnset($key)
    {
        $this->delete($key);
    }
}

