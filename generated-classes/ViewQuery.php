<?php

use Base\ViewQuery as BaseViewQuery;

/**
 * Skeleton subclass for performing query and update operations on the 'view' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class ViewQuery extends BaseViewQuery
{
    public function cacheContains($key){
        $key = str_replace(" ", "_", $key);
        return $this->getCacheBackend()->get($key);
    }

    public function cacheFetch($key)
    {
        $key = str_replace(" ", "_", $key);
        return $this->getCacheBackend()->get($key);
    }

    public function cacheStore($key, $value, $lifetime = 172800)
    {
        $key = str_replace(" ", "_", $key);
        return $this->getCacheBackend()->set($key, $value, $lifetime);
    }

    protected function getCacheBackend(){
        self::$cacheBackend = new Memcached();
        $servers = self::$cacheBackend->getServerList();
        if(is_array($servers)) {
            foreach ($servers as $server){
                if($server['host'] == 'localhost' and $server['port'] == '11211'){
                    return self::$cacheBackend;
                }
            }
        }
        self::$cacheBackend->addServer('localhost', 11211);
        return self::$cacheBackend;
    }
} // ViewQuery
