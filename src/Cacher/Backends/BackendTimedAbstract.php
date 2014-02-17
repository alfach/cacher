<?php namespace Cacher\Backends;

abstract class BackendTimedAbstract extends BackendAbstract {
    
    /**
     * Get the expiration time based on the given minutes.
     *
     * @param  int  $minutes
     * @return int
     */
    protected function expiration($seconds)
    {
        return $seconds ? (time() + $seconds) : 9999999999;
    }
    
    /**
     * Get the expiration time based on the given minutes.
     *
     * @param  int  $minutes
     * @return int
     */
    protected function hasExpired($time)
    {
        return time() >= $time;
    }
}