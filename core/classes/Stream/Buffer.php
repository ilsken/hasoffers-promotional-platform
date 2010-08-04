<?php
/**
 * This is the basic stream buffer class.
 * @author carl
 * @package core-std
 */
class Stream_Buffer extends Stream{
	protected $buffer = '';
	protected $position = 0;
	public function read($bytes){
		$len = strlen($this->buffer);
		if($bytes > $len){
			$ret = $this->buffer;
			$this->buffer = '';
		}else{
			$ret = substr($this->buffer,0,$bytes);
			$this->buffer = substr($this->buffer,$bytes);;
		}
		$this->position += strlen($ret);
		return $ret;
	}
	
	public function write($data){
		$this->buffer .= $data;
	}
	
	public function close(){
		$this->buffer = null;
	}
	
	public function eof(){
		return !is_string($this->buffer);
	}
	public function seek(){
		throw new Exception('Not Implemented');
	}
	public function tell(){
		return $this->position;
	}
}
