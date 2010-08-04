<?php
class Configure {

	private static $instance = null;
	private $data = null;
	private $namespace = null;

	public function __construct() {
		$this->data = array();
	}

	public static function find($namespace = null) {
		$namespace = strtolower($namespace);
		if (!self::$instance) {
			self::$instance = new self();
		}
		if ($namespace) {
			if (!self::$instance->exists($namespace)) {
				self::$instance->write($namespace, null);
			}
			$configure = clone(self::$instance);
			$configure->namespace = $namespace;
			$configure->data =& self::$instance->data;
			$namespace_parts = explode('.', $namespace);
			$len = sizeof($namespace_parts);
			for($i=0; $i<($len); $i++) {
				$configure->data =& $configure->data[$namespace_parts[$i]];
			}
			return $configure;
			
		}
		else {
			return self::$instance;
		}
	}

	public function exists($namespace) {
		$namespace = strtolower($namespace);
		$configure_namespace =& $this->data;

		if (!is_array($configure_namespace)) {
			return false;
		}

		$namespace_parts = explode('.', $namespace);
		$len = sizeof($namespace_parts);

		for($i=0; $i<($len); $i++) {
			if (!array_key_exists($namespace_parts[$i], $configure_namespace)) {
				return false;
			}
			$configure_namespace =& $configure_namespace[$namespace_parts[$i]];
		};
		return true;
	}

	public function delete($namespace = null) {
		$namespace = strtolower($namespace);
		if (is_null($namespace)) {
			foreach ($this->data as $key=>$value) {
				unset($this->data[$key]);
			}
		}
		else {
			if (!$this->exists($namespace)) {
				throw new Exception(
					sprintf(
					'Configure namespace (\'%s\') does not exist. \n' .
					'Make sure you have the correct namespace or use ' .
					'the isset method to make sure the namespace is set.'
					, $this->getFullNamespace($namespace)
					)
				);
			}
				
			$configure_namespace =& $this->data;
	
			$namespace_parts = explode('.', $namespace);
			$len = sizeof($namespace_parts);
			for($i=0; $i<($len-1); $i++) {
				$configure_namespace =& $configure_namespace[$namespace_parts[$i]];
			};
	
			unset($configure_namespace[$namespace_parts[$i]]);
		}
	}
	
	public function read($namespace = null) {
		$namespace = strtolower($namespace);
		if (is_null($namespace)) {
			return $this->data;
		}
		else {
			if (!$this->exists($namespace)) {
				throw new Exception(
					sprintf(
					'Configure namespace (\'%s\') does not exist. \n' .
					'Make sure you have the correct namespace or use ' .
					'the isset method to make sure the namespace is set.'
					, $this->getFullNamespace($namespace)
					)
				);
			}
	
			$configure_namespace =& $this->data;
	
			$namespace_parts = explode('.', $namespace);
			$len = sizeof($namespace_parts);
			for($i=0; $i<($len); $i++) {
				$configure_namespace =& $configure_namespace[$namespace_parts[$i]];
			};
	
			return $configure_namespace;
		}
	}

	public function write($namespace, $value) {
		$namespace = strtolower($namespace);
		$configure_namespace =& $this->data;

		$namespace_parts = explode('.', $namespace);
		$len = sizeof($namespace_parts);
		for($i=0; $i<($len); $i++) {
			$configure_namespace =& $configure_namespace[$namespace_parts[$i]];
		};

		if (is_array($value) && sizeof($value) > 0) {
			if (!is_array($configure_namespace)) {
				$configure_namespace = array();
			}
			foreach ($value as $key => $v) {
				$configure_namespace[$key] = $v;
			}
		}
		else {
			$configure_namespace = $value;
		}
	}
	
	private function getFullNamespace($namespace) {
		$namespace = strtolower($namespace);
		if ($this->namespace) {
			$namespace = $this->namespace . ".". $namespace; 
		}
		return $namespace;
	}

}
