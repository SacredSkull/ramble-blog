<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 24/11/2016
 * Time: 02:59
 */

namespace Ramble\Models;


use RedisException;

class Redis extends Cacher {
    protected $redis = null;
    public function __construct($host, $port) {
        $this->redis = new \Redis();
        $this->redis->connect($host, $port);
        $this->redis->ping();
    }

    public function get($key) {
        return $this->redis->get($key);
    }

    public function set($key, $value, $maxAge = null): bool {
        return $this->redis->set($key, $value, $maxAge);
    }

    public function exists($key): bool {
        return $this->redis->exists($key);
    }
}