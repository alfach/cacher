<?php

namespace Cacher\Backends;

class File extends BackendTimedAbstract {

    private $path;
    
    /*
     * Break each keyspace down into sub-directories
     * 
     * This allows for 36 (a-z0-9) rasied to the power of File::SUBDIRECTORIES; subdirs
     * It keeps the folders smaller - one huge directory to list etc can cause problems
     */
    const SUBDIRECTORIES = 2;

    public function __construct($path, $namespace = '')
    {
        $this->path = $path . DIRECTORY_SEPARATOR;

        $namespace and $this->path .= $namespace . DIRECTORY_SEPARATOR;
    }

    public function get($key)
    {
        $path = $this->key($key);

        // If the file doesn't exist, we can't return the cache so we'll return null.
        // Otherwise, we'll get the contents of the file and read the expiration UNIX timestamp 
        // from the start of the file's contents.
        if (!is_file($path))
        {
            return $this->miss();
        }

        // Protect against multi-threading. Another process could have deleted the file
        // Since we read it
        try {
            $expire = substr($contents = file_get_contents($path), 0, 10);
        } catch (\Exception $e) {
            return $this->miss();
        }

        // If the current time is greater than expiration timestamps we will delete
        // the file and return null. This helps clean up the old files and keeps
        // this directory much cleaner for us as old files aren't hanging out.
        if ($this->hasExpired($expire))
        {
            $this->delete($key);

            return $this->miss();
        }

        $this->hit();

        return unserialize(substr($contents, 10));
    }

    public function put($key, $value, $time = null)
    {
        $value = $this->expiration($time) . serialize($value);

        $this->createCacheDirectory($path = $this->key($key));

        file_put_contents($path, $value);
    }

    public function delete($key)
    {
        $file = $this->key($key);

        try {
            is_file($file) and unlink($file);
        } catch (\Exception $e) {
            //
        }
    }

    public function keep($key, $time = null)
    {
        $path = $this->key($key);

        if (is_file($path))
        {
            try {
                $handle = fopen($path, 'c');

                fwrite($handle, $this->expiration($time));
            } catch (\Exception $e) {
                //
            }
        }
    }
    
    /**
     * Create the file cache directory if necessary.
     *
     * @param  string  $path
     * @return void
     */
    protected function createCacheDirectory($path)
    {        
        try {
            $dir = dirname($path);
            
            is_dir($dir) or mkdir($dir, 0777, true);
        } catch (\Exception $e) {
            if(!is_dir($dir))
            {
                throw $e;
            }
        }
    }

    /**
     * Get the full path for the given cache key.
     *
     * @param  string  $key
     * @return string
     */
    public function key($key)
    {
        $parts = array_slice(str_split($hash = md5($key), static::SUBDIRECTORIES), 0, static::SUBDIRECTORIES);

        return $this->path . join(DIRECTORY_SEPARATOR, $parts) . DIRECTORY_SEPARATOR . $hash;
    }

}
