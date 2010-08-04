<?php
/**
 * This is the standard exception that a USER should SEE.
 * @author carl
 * @package core
 */
class UserException extends Exception{
	protected $data;
	public function __construct($message,$code,$data=null){
		$this->data = $data;
		parent::__construct($message,$code);
	}
	public function getData(){
		return $this->data;
	}
	public static function importException(Exception $e,$data=null){
		if(
			$e instanceof UserException
			|| SERVER_ENVIRONMENT & SERVER_DEV
		){
			return $e;
		}else{
			return new self('An error occured.',$e->getCode(),$data);
		}
	}
}