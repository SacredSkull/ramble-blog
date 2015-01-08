<?php

use Base\ArticleQuery as BaseArticleQuery;

/**
 * Skeleton subclass for performing query and update operations on the 'article' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */

//define('USING_WINDOWS', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));

class ArticleQuery extends BaseArticleQuery
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
		if(USING_WINDOWS == false){
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
		} else {
			self::$cacheBackend = new Memcache();
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
	}
}
