<?php

	class Cache_Memcache implements Cache_Engine {
	
		private $Memcache = null;
	
		public function __construct() {
			$this->Memcache = new Memcache;
			$host = 'localhost';
			$port = '11211';
			if (Conf::read('Cache.Memcache.Server.Host'))
				$host = Conf::read('Cache.Memcache.Server.Host');
				
			if (Conf::read('Cache.Memcache.Server.Port'))
				$port = Conf::read('Cache.Memcache.Server.Port');

			$this->Memcache->addServer($host, $port);
			$this->Memcache->setCompressThreshold(16384, 0.2);
		}
	
		public function __destruct() {
			$this->Memcache->close();
		}
	
		public function get($key = null) {
			//if (Conf::read('DEBUG')) {
			//	$value = $this->Memcache->get($key);
			//} else {
				$value = @$this->Memcache->get($key);
			//}
			return $value;
		}
	
		public function set($key, $data, $lifeLength) {
			//if (Conf::read('DEBUG')) {
			//	$response = $this->Memcache->set($key, $data, MEMCACHE_COMPRESSED, $lifeLength);
			//} else {
				$response = @$this->Memcache->set($key, $data, MEMCACHE_COMPRESSED, $lifeLength);
			//}
			return $response;
		}
	
		public static function isEnabled() {
			return class_exists('Memcache');
		}
	}
