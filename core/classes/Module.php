<?php
/**
 * Module is a basic package that understands it's place in the nth framework.
 * @author carl
 * @package core
 */
class Module{
	const VERSION = .6;
	/**
	 * This is the base path of this class file
	 * @var string
	 */
	protected $path;
	
	/**
	 * This is the class name (So we do not have to run get_class all of the
	 * time.
	 * @var string
	 */
	protected $class;
	
	/**
	 * Creates a Module and sets up the path and class name
	 * @return Module
	 */
	public function __construct($class = null){
		if (!$class) {
			$class = get_class($this);
		}
		$this->class = $class;
		if($this->class === __CLASS__){
			$this->path = SERVER_DIR . '/core/';
		}else{
			$this->path = dirname(Framework::classLocate($this->class));
		}
	}
	
	/**
	 * Locates a resource under this module
	 * @param string $what The relative resource
	 * @return string The path of the resource
	 */
	public function locateInclude($what){
		if(file_exists($what)){
			return $what;
		}
		$dirs = array(
			$this->path
			,SERVER_APP_DIR
			,SERVER_EXT_DIR
			,SERVER_CORE_DIR
		);
				
		foreach($dirs as $dir){
			$file = $dir . '/' . $what;
			if(file_exists($file)){
				return $file;
			}
		}
		throw new Exception('Could not find file: ' . $what . ' in ' . implode("\n", $dirs));
	}
	
	/**
	 * Includes a what with a set of data extracted into scope
	 * @param sting $what
	 * @param array $_data
	 * @return string
	 */
	public function getInclude($what,$_data=array()){
		$file = $this->locateInclude($what);
		extract($_data);
		ob_start();
		include($file);
		return ob_get_clean();
	}
}
