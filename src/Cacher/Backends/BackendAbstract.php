<?php namespace Cacher\Backends;

abstract class BackendAbstract implements BackendInterface {
    
    private $namespace;
    
    private $hits = 0;
    
    private $misses = 0;
    
    public function __construct($namespace = '')
    {        
        $this->namespace = $namespace ? ($namespace . ':') : '';
    }
    
    public function key($key)
    {
        return $this->namespace . $key;
    }
    
    public function hits()
    {
        return $this->hits;
    }
    
    public function misses()
    {
        return $this->misses;
    }
    
    protected function hit()
    {
        $this->hits++;
    }
    
    protected function miss()
    {
        $this->misses++;
    }
}