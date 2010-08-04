<?php
class Debug {
	
	protected static $data = array();
	
	public static function store($debuggable, $min_debug_level = 2) {
		if (!$min_debug_level) $min_debug_level = 1;
		
		if (Conf::read('DEBUG') >= $min_debug_level ) {
			$trace = debug_backtrace();
			$file = $trace[0]['file'];
			$line = $trace[0]['line'];
			
			self::$data[] = array(
				'file' => $file,
				'line' => $line,
				'data' => $debuggable
			);
			
			$path = null;
			if (!file_exists($path)) {
				$path = SERVER_APP_DIR . '/../log/debug';
			}
			
			error_log("{$file}:{$line} - $debuggable\n", 3, $path);
	
		}
	}
        
        public static function write($msg){Debug::store($msg, 1);}
	
	public static function retrieve() {
		return self::$data;
	}
	
}