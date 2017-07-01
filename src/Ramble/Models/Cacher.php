<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 24/11/2016
 * Time: 02:53
 */

namespace Ramble\Models;


class Cacher {
    private $db = [];
    public function get($key){
        if(!in_array($key, $this->db))
            return false;
        return $this->db[$key];
    }
    public function set($key, $value, $maxAge = null): bool {
        $this->db[$key] = $value;
        return true;
    }
    public function exists($key): bool {
        if(!in_array($key, $this->db))
            return false;
        return true;
    }
}