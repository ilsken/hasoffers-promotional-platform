<?php

	class Cache {
		private static $Instance = null;
		public static $cacheLifeLength = 84000;
		protected static $prefix = '';
	
		public static function addPrefix($prefix) {
			self::setPrefix(self::$prefix . $prefix);
		}
		
		public static function setPrefix($prefix) {
			self::$prefix = $prefix;
		}
		
		public static function get($key = null) {
			try {
				$key = self::$prefix.$key;
				return Cache::$Instance->get($key);
			} catch (Exception $e) {
				//if (Conf::read('DEBUG')) {
				//	throw $e;
				//} else {
					return null;
				//}
			}
		}
	
		public static function getObject($key = null, $class = null) {
			if (!is_null($class))
				$key = "$class($key)";
			$obj = self::get($key);
			if (is_string($obj))
				return unserialize($obj);
			if (is_object($obj))
				return $obj;
			return null;
		}
	
		public static function set($key, $data, $lifeLength = null) {
			try {
				$key = self::$prefix.$key;
				if (is_null($lifeLength))
					$lifeLength = self::$cacheLifeLength;

				if ( !$data ) return null;

				return Cache::$Instance->set($key, $data, $lifeLength);
			} catch (Exception $e) {
				//if (Conf::read('DEBUG')) {
				//	throw $e;
				//} else {
					return null;
				//}
			}
		}
	
		public static function setObject($object, $key = null, $lifeLength = null) {
			if (is_null($key)) {
				if (method_exists($object, 'getCacheKey'))
					$key = $object->getCacheKey();
				elseif (property_exists($object, 'id'))
					$key = $object->id;
				else
					throw new Exception('Unable to generate a unique key, please create a getCacheKey function');
			}

			$key = get_class($object).'('.$key.')';

			return self::set($key, serialize($object), $lifeLength);
		}
	
		public static function initalize($engine = 'Cache_Static') {
			try {
				// Handle engine failover here
				switch ($engine) {
					case 'Cache_StaticMemcache':
						if (Cache_StaticMemcache::isEnabled())
							break;
					case 'Cache_Memcache':
						if (Cache_Memcache::isEnabled())
							break;
					case 'Cache_Static':
					default:
						$engine = 'Cache_Static';
				}
	
				if (is_null(Cache::$Instance))
					Cache::$Instance = new $engine();
			} catch (Exception $e) {
				//if (Conf::read('DEBUG')) {
				//	throw $e;
				//} else {
					return null;
				//}
			}
		}
	}
	
	if (Conf::exists('Cache.Engine'))
		Cache::initalize(Conf::read('Cache.Engine'));
	else
		Cache::initalize();

	if (Conf::exists('Cache.Life'))
		Cache::$cacheLifeLength = Conf::read('Cache.Life');
