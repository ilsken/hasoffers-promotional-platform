<?php
/**
 * This is responsible for serving requests from the location_handler
 * @author carl
 * @package core
 */
class Server{
	############################################################################
	# Statics
	############################################################################	
	private static $instance = false;
	private static $cache_mtime =false;
	private static $cache_id = false;
	private static $cache_expire = false;
	
	/**
	 * Returns the current server instance
	 *
	 * @return Server
	 */
	public static function find(){
		if(!self::$instance){
			self::$instance = new self();
		}
		return self::$instance;
	}

	######################################
	# Cache setters
	######################################
	/**
	 * Sets the cache modified time.
	 * @param $mtime
	 */
	public static function setCacheMtime($mtime){
		self::$cache_mtime = $mtime;
	}
	
	/**
	 * Sets the etag-id
	 * @param $id
	 */
	public static function setCacheId($id){
		self::$cache_id = $id;
	}
	
	/**
	 * Sets the future expires time 
	 * @param $time
	 */
	public static function setCacheExpire($time){
		self::$cache_expire = $time;
	}
	
	/**
	 * Grabs the route information for a class and a method.
	 * @param $what
	 * @param $method
	 * @return unknown_type
	 */
	public static function getRouteRpcInfoFor($what,$method){
		if(!class_exists($what,true)){
			return false;
		}
		$rc = new ReflectionClass($what);
		
		$route_okay = $rc->getStaticPropertyValue('route_okay',array());
		$real_method = false;
		$arg_count = null;
		if(isset($route_okay[$method])){
			if(is_array($route_okay[$method])){
				if(isset($route_okay[$method]['method'])){
					$real_method = $route_okay[$method]['method'];
					$arg_count = isset($route_okay[$method]['arg_count'])
						? $route_okay[$method]['arg_count']
						: null
					;
				}else{
					$real_method = $method;
				}
			}else{
				$real_method = $route_okay[$method];
			}
		}else{
			if(in_array($method,$route_okay)){
				
				$real_method = $method;
			}else{
				$parent_class = $rc->getParentClass();
				if($parent_class){
					return self::getRouteRpcInfoFor($parent_class->name,$method);
				}else{
					return false;
				}
				
			}
		}
		
		$rmethod = $rc->getMethod($real_method);
		$static = $rmethod->isStatic();
		
		return array('arg_count'=>$arg_count,'method'=>$real_method,'static'=>$static);
	}
		
	############################################################################
	# Member variables
	############################################################################
	protected $url;
	protected $host;
	
	############################################################################
	# Constructor
	############################################################################
	/**
	 * Creates a new server for the specified host and url 
	 * @param $host
	 * @param $url
	 */
	public function __construct($host,$url){
		$this->host = $host;
		$this->url = $url;
		self::$instance = $this;
	}
	
	/**
	 * Redirects a client
	 * @param string $url
	 */
	public static function redirect($url){
		header('Location: ' . $url);
		die();
	}
	
	/**
	 * Processes a url and creates 
	 * @param $url
	 * @return unknown_type
	 */
	public function processRequest($url){
		$url = Framework::classRouteTranslate($url);

		// Determine the root class/method
		$url_parts = explode('/',substr($url,1));
		$root_class = array_shift($url_parts);
		$method = array_shift($url_parts);
		
		// If there is no method, specify index
		if(!$method){
			$method = 'index';
		}

		if(!class_exists($root_class)){
			$method = $root_class;
			if (preg_match('/.(html|php)$/', $method)) {
				$method = preg_replace('/.(html|php)$/', '', $method);
			}
			$root_class = SERVER_DEFAULT_ROUTE_CLASS;
			$method_info = self::getRouteRpcInfoFor($root_class,$method);
		}else{
			// Grabbing a method
			$method_info = self::getRouteRpcInfoFor($root_class,$method);
		}
		
		// If the method exists
		if($method_info){
			
			$route_arg_count = $method_info['arg_count'];
			$method = $method_info['method'];
			$static = $method_info['static'];
			
			if(is_null($route_arg_count)){
				$route_args = $url_parts;
				$url_parts = array();
			}else{
				$route_args = array_slice($url_parts,0,$route_arg_count);
				$url_parts = array_slice($url_parts,$route_arg_count);
			}
			
			if(!$static){
				$root_class = new $root_class();
			}
			
			$scope = call_user_func_array(array($root_class,$method),$route_args);
		}else{
			// 404
			return $this->serveNotFound($url);
		}
		
		
		// Run the scope
		if(is_object($scope)){
			do{
				$method_info = self::getRouteRpcInfoFor($scope,$method);
				if($method_info){
					$route_arg_count = $method_info['arg_count'];
					$method = $method_info['method'];
					
					$route_args = array_slice($url_parts,0,$route_arg_count);
					$url_parts = array_slice($url_parts,$route_arg_count);
					
					$scope = call_user_func_array(array($scope,$method),$route_args);
				}else{
					return $this->serveNotFound($url);
				}
			}while(is_object($scope) && $method = array_shift($url_parts));
		}
		
		// It should be text by now.
		return $scope;
	}
	
	/**
	 * Processes the host/url.
	 * @return string
	 */
	public function serve(){
		if(substr($this->url,-1) == '/'){
			$this->url .= 'index.php';
		}
		
		// App WWW
		$file = SERVER_DIR . '/app/www/' . $this->url;
		if(file_exists($file)){
			return $this->serveFile($file);
		}
		
		// Core WWW
		$file = SERVER_DIR . '/core/www/' . $this->url;
		if(file_exists($file)){
			return $this->serveFile($file);
		}
		
		// Module
		return $this->serveCache($this->processRequest($this->url));
	}
	
	/**
	 * Serves the string from the client's cache if possible
	 * @param $content
	 * @param $id
	 * @return unknown_type
	 */
	public function serveCache($content,$id=null){
		# Caching is only okay if we have not sent headers
		if(!headers_sent()){
			# Expiration cache
			$expires = self::$cache_expire;
			if($expires){
				header("Cache-Control: must-revalidate");
				header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expires) . " GMT");
			}
			
			# Modified cache
			$last_modified_time = self::$cache_mtime;
			if($last_modified_time){
				header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT");
				if(@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time ){
					header("HTTP/1.1 304 Not Modified");
					exit;
				}
			}
			
			# Etag cache
			if(!$id){
				if(self::$cache_id){
					$id = self::$cache_id;
				}else{
					$id = md5($content);
				}
			}
			if($id){
				header("Etag: $id");
				if(isset($_SERVER['HTTP_IF_NONE_MATCH'])){
					if(trim($_SERVER['HTTP_IF_NONE_MATCH']) == $id){
						header("HTTP/1.1 304 Not Modified");
						exit;
					}
				}
			}
		}
		return $this->serveContent($content);
	}
	
	/**
	 * Serves content
	 * @param string $content
	 * @return string
	 */
	public function serveContent($content){
		return $content;
	}
	
	/**
	 * Serves a file
	 * @param string $file
	 * @return string
	 */
	public function serveFile($file){
		$type = MimeType::getType($file);
		if(!headers_sent()){
			header('Content-Type: ' . $type);
		}
		
		if(strpos($type,'text/html')!==false){
			$tmpl = new Templator();
			$tmpl->setContent($tmpl->getInclude($file));
			return $this->serveCache($tmpl->getDisplay());
		}else{
			self::setCacheMtime(filemtime($file));
			return $this->serveCache(file_get_contents($file));
		}
	}
	
	/**
	 * 404!
	 * @return string
	 */
	public function serveNotFound(){
		$templator = new Templator();
		return $this->serveCache($templator->notFound());
	}
	
}
