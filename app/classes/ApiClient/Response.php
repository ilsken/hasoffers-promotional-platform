<?
class ApiClient_Response {

	private
		$request = null,
		$data    = null
	;

	protected static $last_response = null;

	protected function __construct(HttpRequest $request) {
		$this->request = $request;
		$this->data = $this->parseData();
		self::$last_response = $this;
	}

	public static function create(HttpRequest $request) {
		$response = new self($request);
		return $response;
	}

	public static function getLastResponse() {
		return self::$last_response;
	}

	public function getHttpRequest() {
		return $this->request;
	}

	public function getRequestArgs() {
		return $this->request_args;
	}

	public function getData() {
		return $this->data;
	}

	public function getValue() {
		if ($this->isSuccess()) {
			if (isset($this->data['response']['data'])) {
				return $this->data['response']['data'];
			}
		}
		else {
			// Do something?  Throw exception?
		}
	}

	public function isSuccess() {
		if (isset($this->data['response']['status'])) {
			return $this->data['response']['status'] > 0;
		}
	}

	public function getError() {
		if ($this->isSuccess() == false) {
			if (isset($this->data['response']['data'])) {
				return $this->data['response']['data'];
			} else if (!is_null($this->data)) {
				return $this->data;
			} else {
				return "Json could not be decoded";
			}
		}
	}

	public function getErrorName() {
		if ($this->isSuccess() == false) {
			if (is_array($this->data) && isset($this->data['response']['data']['error_name'])) {
				return $this->data['response']['data']['error_name'];
			}
			else {
				return "Unknown Error";
			}
		}
	}

	public function getErrorCode() {
		if ($this->isSuccess() == false) {
			if (isset($this->data['response']['data']['error_code'])) {
				return $this->data['response']['data']['error_code'];
			}
		}
	}

	public function getErrorMessage() {
		if ($this->isSuccess() == false) {
			if (is_array($this->data)) {
				if (isset($this->data['response']['data']['public_message'])) {
					return $this->data['response']['data']['public_message'];
				}
				else {
					return $this->data['response']['data'];
				}
			} else {
				return $this->data;
			}
		}
	}
	
	public function getValidationErrors() {
		if ($this->isSuccess() == false) {
			if (is_array($this->data)) {
				if (isset($this->data['response']['data']['errors'])) {
					return $this->data['response']['data']['errors'];
				}
			}
			return array();
		}
	}

	protected function parseData() {
		$json_data = $this->getHttpRequest()->getResponseBody();
		$data = json_decode($json_data, true);
		return $data;
	}

}
