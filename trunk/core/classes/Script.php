<?php
/**
 * This is the basic CLI script runner. Anything that extends it and is in 
 * a subdir of the core dirs under script (eg: app/scripts/myClass.php) is auto
 * loaded (nth myClass)
 * @author carl
 * @package core
 */
class Script{
	/**
	 * Creates a script node
	 *
	 * @param array $options
	 * @param unknown_type $in_stream
	 * @param unknown_type $out_stream
	 */
	public function __construct(
		array $options = array()
		,$in_stream=null
		,$out_stream=null
	){
		$this->options = $options;
		$this->in_stream = $in_stream;
		$this->out_stream = $out_stream;
	}
	
	/**
	 * Writes output to a stream
	 */
	public function write($msg){
		if($this->out_stream){
			fwrite($this->out_stream,$msg);
		}else{
			echo $msg;
		}
	}
	
	/**
	 * Reads input from a stream
	 */
	public function read($length=1024){
		if($this->in_stream){
			return fread($this->in_stream,$length);
		}else{
			return '';
		}
	}
	public static function parse($cmd_args,$stdin_stream=null,$stdout_stream=null){
		if(!$stdin_stream){
			$stdin_stream = fopen('php://stdin','r');
		}
		if(!$stdout_stream){
			$stdout_stream = fopen('php://stdout','w');;
		}
		
		array_shift($cmd_args);
		$actions = array();
		$options = array();
		
		foreach($cmd_args as $arg){
			if(substr($arg,0,1) === '-'){
				$arg = preg_replace('/^-*/','',$arg);
				$arg_parts = explode("=",$arg);
				if(!isset($arg_parts[1])){
					$arg_parts[1] = true;
				}
				$options[$arg_parts[0]] = $arg_parts[1];
			}else{
				$actions[] = $arg;
			}
		}
		
		$first = array_shift($actions);
		if(!$first){
			$first = 'Nth';
		}
		
		$script_file = Framework::scriptLocate($first);
		if(!$script_file){
			array_unshift($actions,$first);
			$first = 'Nth';
			$script_file = Framework::scriptLocate($first);
		}
		
		if($script_file){
			require($script_file);
			$class = $first;
			$action = array_shift($actions);
		}else{
			throw new Exception('Script not found');
		}
		
		$scope = new $first($options,$stdin_stream,$stdout_stream);
		while($scope instanceof Script){
			if($scope->isValidMethod($action,$scope)){
				$scope = $scope->$action($actions,$options);
				$action = array_shift($actions);
			}else{
				$action = 'help';
			}
		}
	}
	
	public static function isValidClass($class){
		return (
			class_exists($class,true)
			&& in_array(__CLASS__,class_parents($class))
		);
	}
	
	public static function isValidMethod($method_name,$class){
		if(is_object($class)){
			$class = get_class($class);
		}
	
		if(
			$method_name !== 'help'
			&&method_exists(__CLASS__,$method_name)
		){
			return false;
		}
		if(
			class_exists($class,true)
			&& method_exists($class,$method_name)
		){
			return true;
		}
	}
	
	
	/**
	 * Find out more about this script's commands.
	 * 
	 *Usage:
	 * nth help [command]
	 * nth module help
	 * nth module help [command]
	 */
	public function help($with=null){
		$this->write("Usage: nth script action [ARGS] [[--option=setting]]\n");
		$this->write("Usage: nth action [ARGS] [[--option=setting]]\n\n");
		if(is_array($with)){
			$what = array_shift($with);
		}else{
			$what = $with;
		}
		if($this->isValidMethod($what,$this)){
			$this->describeMethod(new ReflectionMethod($this,$what),true);
		}else{
			$this->describeClass(new ReflectionClass($this));
		}
	}
	
	public function helpScript($script){
		
	}
	public function describeScript(ReflectionClass $class, $verbose=false){
		return $class->name . ' -' . $this->getDocumentation($class,$verbose);
	}
	
	public function describeClass(ReflectionClass $class){
		$this->describeModule($class);
		$this->write("\nActions:\n");
		foreach($class->getMethods() as $method){
			if($this->isValidMethod($method->name,$class->name)){
				$this->describeMethod($method);
			}
		}
	}
	public function describeModule(ReflectionClass $class){
		$this->write("Documentation: \n");
		$this->write($this->getDocumentation($class));
	}
	public function getDocumentation($reflection, $verbose = false){
		$out = '';
		$documentation = $reflection->getDocComment();
		if(!$documentation){
			$documentation = "No documentation found! Read the source!\n";
			$documentation .= $reflection->__toString();
		}
		$doc_lines = explode("\n",$documentation);
		foreach($doc_lines as $line){
			$line = preg_replace("/^\s*[\/\*]+\s?/",'',$line);
			if($line){
				$out .= (" $line\n");
				if(!$verbose){
					break;
				}
			}
		}
		return $out;
	}
	public function describeMethod(ReflectionMethod $method, $long=false){
		$this->write(" " . $method->name . "\t-");
		$documentation = $method->getDocComment();
		if(!$documentation){
			$documentation = "No documentation found! Read the source!\n";
			$documentation .= $method->__toString();
		}
		$doc_lines = explode("\n",$documentation);
		foreach($doc_lines as $line){
			$line = preg_replace("/^\s*[\/\*]+/",'',$line);
			if($line){
				$this->write(" $line\n");
				if(!$long){
					return;
				}
			}
		}
	}
}
