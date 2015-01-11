cacher
======

[![Build Status](https://travis-ci.org/mrjgreen/cacher.svg?branch=master)](https://travis-ci.org/mrjgreen/cacher) [![Coverage Status](http://img.shields.io/coveralls/mrjgreen/cacher.svg)](https://coveralls.io/r/mrjgreen/cacher)

A simple stackable PHP caching library with Redis, File, Memory (array) and Custom ArrayAccess backends


Installation
------------
Install via composer

```
{
    "require": {
        "mrjgreen/cacher": "1.*"
    }
}

```

### Usage

```PHP

$backend = new Cacher\Backends\File('path/to/tmpstorage');

$cache = new Cacher($backend);

$cache->set('key', 'value');

$cache->get('key'); // returns 'value'

```

### Stacking

```PHP

$fileBackend = new Cacher\Backends\File('path/to/tmpstorage');

// Uses any compatible redis library. EG nrk/predis, irediscent/irediscent
$redisBackend = new Cacher\Backends\Redis(new Predis\Client($config));

$stackedCache = new Cacher($redisBackend, new Cacher($fileBackend));

// Looks in redis then falls back to file before calling the callback function
$stackedCache->get('key', function(){
  return 'value';
}); 

```
