<?php
class ApiClient_HasOffers_Request extends ApiClient_Request {
	/**
	 * The version of the HasOffers module to access
	 * @var int
	 */
	protected $version = null;
	
	/**
	 * A string that provide information about the Service accessing the
	 * internal Api
	 * @var string
	 */
	protected static $signature = null;
	
    public function getDefaultUrl() {
        return Configure::find('ApiClient')->read('Endpoint');
    }
    
    public function setVersion($version) {
    	$this->version = $version;
    }
    
    public function getVersion() {
    	if ($this->version) {
    		return $this->version;
    	} else {
    		return $this->getDefaultVersion();
    	}
    }
    
    public function setNetworkId($network_id) {
    	$this->network_id = $network_id;
    }
    
    public function getNetworkId() {
    	return Configure::find('ApiClient')->read('NetworkId');
    }
    
    public static function addSignatureToken($signature_token) {
    	if (self::$signature) {
    		$signature_token = "-" . $signature_token;
    	}
    	self::$signature = self::$signature . $signature_token;
    }
    
    public static function getSignature() {
   		return self::$signature;
    }
    
    public function getDefaultVersion() {
    	return Configure::find('ApiClient')->read('Version');
    }
	
    public function getNetworkToken() {
    	return Configure::find('ApiClient')->read('NetworkToken');
    }
    
    public function getRequestArgs() {
    	$arguments = parent::getRequestArgs();
    	$arguments['Service'] = 'HasOffers';
    	$arguments['Version'] = $this->getVersion();
    	$arguments['NetworkId'] = $this->getNetworkId();
    	$arguments['NetworkToken'] = $this->getNetworkToken();
		
    	return $arguments;
    }
    
	public static function create(
		$target = null, $method = null,
		array $args = null, $type = null
	) {
		$request = new self();
		$request->setTarget($target);
		$request->setMethod($method);
		$request->setArgs($args);
		$request->setType($type);
		return $request;
	}
	
	public static function execute(
		$target, $method, array $args = null, $type = null
	) {		
		$request = self::create($target, $method, $args, $type);
		$result = $request->send();
		return $result;
	}
	
	

}