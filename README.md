cacher
======

[![Build Status](https://travis-ci.org/joegreen0991/cacher.svg?branch=master)](https://travis-ci.org/joegreen0991/cacher) [![Coverage Status](http://img.shields.io/coveralls/joegreen0991/cacher.svg)](https://coveralls.io/r/joegreen0991/cacher)

A simple stackable PHP caching library with Redis, File, Memory (array) and Custom ArrayAccess backends


Installation
------------
Install via composer

```
{
    "require": {
        "joegreen0991/cacher": "dev-master"
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

// Uses Predis library
$redisBackend = new Cacher\Backends\Redis(new Predis\Client($config));

$stackedCache = new Cacher($redisBackend, new Cacher($fileBackend));

// Looks in redis then falls back to file before calling the callback function
$stackedCache->get('key', function(){
  return 'value';
}); 

```
