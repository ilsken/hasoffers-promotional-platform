<?php

	class Cache_Static implements Cache_Engine {
	
		private $Cache = array();
	
		public function get($key = null) {
			if (isset($this->Cache[$key]))
				return $this->Cache[$key]['data'];
			return null;
		}
	
		public function set($key, $data, $lifeLength) {
			$this->Cache[$key] = array('data' => $data) ;

			return true;
		}
	
		public static function isEnabled() {
			return true;
		}
	}
