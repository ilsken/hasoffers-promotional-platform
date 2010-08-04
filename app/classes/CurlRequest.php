<?php

class CurlRequest {
	public $options = array(
		CURLOPT_RETURNTRANSFER => true,     // return the web page		
		CURLOPT_HEADER         => false,    // don't return the headers
		CURLOPT_FOLLOWLOCATION => true,     // follow redirects
		CURLOPT_USERAGENT      => "HasOffers Promotional Platform v1.0", // set a normal looking useragent
		CURLOPT_AUTOREFERER    => true,     // set referer on redirect
		CURLOPT_CONNECTTIMEOUT => 120,      // timeout
		CURLOPT_TIMEOUT        => 120,      // timeout
		CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
	);
	
	private $response, $headers, $url, $method, $querydata, $postfields;

	public function __construct() {
	}
	
	public function send() {
		$request = curl_init();
		
		if ( $this->querydata )
			$url = http_build_url($this->url, array("query" => $this->querydata));
		else
			$url = $this->url;
	
		curl_setopt($request, CURLOPT_URL, $url);		
	
		curl_setopt_array($request, $this->options );	
		
		if (strtoupper($this->method) == 'POST') {
			curl_setopt($request, CURLOPT_POST, true);
			
			if ( $this->postfields )
				curl_setopt($request, CURLOPT_POSTFIELDS, $this->postfields);	
		}
		
		$this->response = curl_exec($request);	

		if ( $this->response )
			$this->headers  = curl_getinfo($request);
			
		curl_close($request);			
	}

	public function setUrl( $url ) {
		$this->url = $url;
	}
	
	public function setMethod( $method ) {
		$this->method = $method;
	}
	
	public function setQueryData( $query ) {
		if ( $query && is_array($query) )
			$this->querydata = http_build_str($query);		
		else 
			$this->querydata = $query;		
	}
	
	public function addPostFields( $data ) {
		if ( $data && is_array($data) )
			$this->postfields = http_build_str($data);		
		else 
			$this->postfields = $data;
	}
	
	public function getResponseBody() {
		return $this->response;
	}
}
