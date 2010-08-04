<?
class ApiClient_Request {

    protected
		$url    = null,
        $target = null,
        $method = null,
        $args   = array(),
		$type   = null
    ;
	
	private
		$request = null
	;

	protected function __construct() {
		$this->request = new HttpRequest(null);
	}
	
	public function getHttpRequest() {
		return $this->request;
	}

	public function setUrl($url) {
		$this->url = $url;
	}

    public function getDefaultUrl() {
        return Configure::find('ApiClient')->read('Endpoint');
    }

    public function getUrl() {
        if (is_null($this->url)) {
            $this->url = $this->getDefaultUrl();
        }
        return $this->url;
    }

    public function setTarget($target) {
        $this->target = $target;
    }

    public function getTarget() {
        return $this->target;
    }

    public function setMethod($method) {
        $this->method = $method;
    }

    public function getMethod() {
        return $this->method;
    }

	public function setArgs(array $args = null) {
		if ($args) {
	        $this->args = array_merge($this->args, $args);
		}
	}

	public function getArgs() {
        return $this->args;
    }

	public function getRequestArgs() {
        $arguments = array(
            'Target'    => $this->getTarget(),
            'Method'    => $this->getMethod()
        );
        foreach ($arguments as $var => $value) {
            if (is_null($value)) {
                unset($arguments[$var]);
            }
        }
		return $arguments;
	}
	
	public function getAllArgs() {
		return array_merge($this->getArgs(), $this->getRequestArgs());
	}

	public function clearArgs() {
        $this->args = array();
    }

	public function setType($type) {
		if (in_array(strtolower($type), array('get', 'post'))) {
			$this->type = $type;
		}
	}

	public function getDefaultType() {
		return "POST";
	}

	public function getType() {
		if (is_null($this->type)) {
			$this->setType($this->getDefaultType());
		}
		return $this->type;
	}

	public function send() {
		try {
			$request = $this->getHttpRequest();
	        $request->setUrl($this->getUrl());
			$request->setMethod($this->getTypeInt());
			
			switch (strtoupper($this->getType())) {
				case 'GET':
					$request->setQueryData($this->getAllArgs());
				break;
				case 'POST': // |
				case 'PUT':  // v TODO Test PUT request types
					$request->setQueryData($this->getRequestArgs());
					$request->addPostFields($this->getArgs());
				break;
			}
			
			$request->send();
			$response = ApiClient_Response::create($request);
			return $response;
		} catch (Exception $e) {
			$message = "
			API Request Failed:<br />
			url: {$this->getUrl()} <br />
			params: {$this->getAllArgs()}<br />
			";
			die($message);
		}
	}

	protected function getTypeInt() {
		if ($type = $this->getType()) {
			return constant('HTTP_METH_' . $type);
		}
	}

}
