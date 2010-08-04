<?php
class Session {
	
	protected static $instance = null;
	protected $session = null;
	
	protected function __construct() {
		session_start();
	}	
	
	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	} 
	
	public static function exists($varname) {
		$session = self::getInstance();
		return array_key_exists($varname, $_SESSION);
	}
	
	public static function read($varname) {
		$session = self::getInstance();
		if (array_key_exists($varname, $_SESSION)) {
			return $_SESSION[$varname];
		} else {
			return null;
		}
	}
	
	public static function write($varname, $value) {
		$session = self::getInstance();
		$_SESSION[$varname] = $value;
	}
	
	public static function delete($varname) {
		$session = self::getInstance();
		if (array_key_exists($varname, $_SESSION)) {
			unset($_SESSION[$varname]);
		}
	}
	
	
	
}