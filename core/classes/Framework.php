<?php
/**
 * This class is responsible for understanding where the locations are
 * and including or testing for the existence of files.
 * @author carl
 * @package core
 */
class Framework{
	
	/**
	 * Constant to specify the Core part of the framework.
	 */
	const CORE = 1;

	/**
	 * Constant to specify the App part of the framework.
	 */
	const APP = 2;

	/**
	 * Constant to specify the Extensions part of the framework.
	 */ 
	const EXT = 4;

	/**
	 * The routes that are manually overridden.
	 * @var array
	 */
	private static $class_routes = array();
	
	/**
	 * Additional roots for autoloading.
	 * @var array
	 */
	private static $custom_root_paths = array();

	/**
	 * @desc Adds a class route
	 * @param string $route_name
	 * @param mixed $options
	 * @return null
	 */
	public static function classRouteAdd($route_name,$options){
		self::$class_routes[$route_name] = $options;
	}
	
	public static function classRouteTranslate($url){
		if(substr($url,0,strlen(SERVER_BASE_URL)) == SERVER_BASE_URL){
			$url = substr($url,strlen(SERVER_BASE_URL)); 
		} 
		foreach(self::$class_routes as $route_match=>$call_specs){
			$match = preg_match($route_match,$url,$matches);
			$translate_array = array();
			if($match){
				foreach ($call_specs as $call_spec) {
					if (preg_match('%(\$|\\{2})(\d)+%', $call_spec, $spec_matches)) {
						$translate_array[] = $matches[$spec_matches[2]];
					}
					else {
						$translate_array[] = $call_spec;
					}
				}
				$url = implode("/", $translate_array);
			}
		}
		return $url;
	}
	
	public static function classLocate($class_name,$class_dir='classes'){
		$class_name = str_replace('_','/',$class_name);
		$roots = self::getRootPaths();
		$file = false;

		foreach($roots as $root){
			// Check classes
			$file = $root . "/$class_dir/". $class_name . '.php';
			if(file_exists($file)){
				break;
			}
			
			// Check class namespaces
			if ($root == SERVER_APP_DIR && APP_CLASS_NAMESPACES) {
				foreach (explode(':', APP_CLASS_NAMESPACES) as $ns) {
					$file = $root . "/$class_dir/$ns/$class_name.php";
					if (file_exists($file)) {
						break 2;
					}
				}
			}
			
			// Check modules
			$file = $root. '/modules/'.$class_name.'/'. $class_name . '.php';
			if(file_exists($file)){
				break;
			}
			
			// Prep module classes
			$pos = strpos($class_name,'/');
			if($pos){
				$module_name = substr($class_name,0,$pos);
				$module_class_name = substr($class_name,$pos+1);
				// Check module classes
				$file = $root . '/modules/'.$module_name."/$class_dir/". $module_class_name . '.php';
				if(file_exists($file)){
					break;
				}
			}
			
			// Check root class
			$file = "$root/$class_name/$class_name.php";
			if(file_exists($file)){
				break;
			}else{
				$file = false;
			}
		}
		if(!$file){
			//return self::scriptLocate($class_name);
		}
		return $file;
	}
	
	public static function addCustomRoot($name, $path) {
		self::$custom_root_paths[$name] = $path;
	}
	
	public static function deleteCustomRoot($name) {
		if (array_key_exists(self::$custom_root_paths[$name])) {
			unset(self::$custom_root_paths[$name]);
		}
	}
	
	public static function getRootPaths() {
		$roots = array(SERVER_APP_DIR, SERVER_EXT_DIR, SERVER_CORE_DIR);
		$roots = array_merge(
			$roots,
			explode(':', AUTOLOAD_PATH),
			array_values(self::$custom_root_paths)
		);
		return $roots;
	}
	
	public static function moduleInstalled($module_name){
		$roots = self::getRootPaths();
		foreach($roots as $root){
			$file = $root .'/modules/'.$module_name;
			if(file_exists($file)){
				return $file;
			}
		}
	}
	
	public static function moduleList($like=null,$type=null){
		if (is_null($type)) {
			$type = self::CORE | self::APP | self::EXT;
		}
		$roots = self::getRootPaths();
		$results = array();
		foreach($roots as $root){
			$dir = $root .'/modules/';
			if(file_exists($dir)){
				foreach(scandir($dir) as $file){
					if(is_dir($dir . '/' .$file) && substr($file,0,1) !== '.'){
						if(!$like || preg_match("/$like/",$file)){
							$results[] = $file;
						}
					}
				}
			}
		}
		
		return $results;
	}

	public static function moduleInstall($module_name){
		throw new Exception('TODO:');
	}
	
	public static function confLoad($module_name='app',$name='environment',$all = false,$only_once=true){
		$found = false;
		$roots = self::getRootPaths();
		foreach($roots as $root){
			// Scan modules
			$file = $root . '/modules/' . $module_name . '/conf/' . $name . '.php';
			if(file_exists($file)){
				if($only_once){
					require_once($file);
				}else{
					require($file);
				}
				if(!$all){
					return true;
				}else{
					$found = true;
				}
			}
			$file = $root .'/conf/'.$name.'.php';
			// Scan the root.
			if(file_exists($file)){
				if($only_once){
					require_once($file);
				}else{
					require($file);
				}
				if(!$all){
					return true;
				}else{
					$found = true;
				}
			}
		}
		
		return $found;
	}
	
	public static function scriptLocate($name){
		self::scriptList($scripts);
		if(isset($scripts[$name])){
			return $scripts[$name];
		}
		return false;
	}
	
	public static function scriptList(&$name_file=null){
		$name_file = array();
		$modules = self::moduleList();
		$roots = self::getRootPaths();
		$results = array();
		foreach($modules as $module){
			$module_dir = dirname(self::classLocate($module));
			$script_dir = $module_dir .'/scripts/';
			if(is_dir($script_dir)){
				foreach(scandir($script_dir) as $file){
					if(substr($file,0,1) !== '.'){
						$basename = basename($file);
						$basename = substr($basename,0,strpos($basename,'.'));
						if($basename){
							$results[] = $module.'_'.$basename;
							$name_file[$module.'_'.$basename] = $script_dir.'/'.$file;
						}
					}
				}
			}
		}
		foreach($roots as $root){
			$script_dir = $root .'/scripts/';
			if(is_dir($script_dir)){
				foreach(scandir($script_dir) as $file){
					if(substr($file,0,1) !== '.'){
						$basename = basename($file);
						$basename = substr($basename,0,strpos($basename,'.'));
						if($basename && $basename !== 'nth'){
							$results[] = $basename;
							$name_file[$basename] = $script_dir.'/'.$file;
						}
					}
				}
			}
		}
		return array_unique($results);
	}
}
