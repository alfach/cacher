<?php namespace Cacher\Backends;

class Memory extends BackendTimedAbstract {
    
    protected $store = array();
    
    public function get($key)
    {
        if(isset($this->store[$key])) 
        {
            if(!$this->hasExpired($this->store[$key][0]))
            {
                $this->hit();
                
                return $this->store[$key][1];
            }
            
            unset($this->store[$key]);
        }
        
        $this->miss();
    }
    
    public function put($key, $value, $time = null)
    {        
        $key = $this->key($key);
        
        $this->store[$key] = array(
            $this->expiration($time),
            $value
        );
        
        return $value;
    }
    
    public function delete($key)
    {        
        $key = $this->key($key);
        
        if(isset($this->store[$key])) 
        {
            unset($this->store[$key]);
        }
    }
    
    public function keep($key, $time = null)
    {        
        $key = $this->key($key);
        
        if(isset($this->store[$key]))
        {
            $this->store[$key][0] = $this->expiration($time);
        }
    }

    public function ttl($key)
    {
        if(isset($this->store[$key]))
        {
            return $this->getTtl($this->store[$key][0]);
        }
    }
}
