<?php
/**
 * The basic stream class
 * @author carl
 * @package core-std
 */
class Stream{
	protected $handle;
	public function __construct($handle=null){
		$this->handle = $handle;
	}
	public function setStream($handle){
		$this->handle = $handle;
	}
	
	public function read($bytes,&$bytes_skipped=null){
		if($this->handle instanceof Stream){
			return $this->handle->read($bytes);
		}else{
			return fread($this->handle,$bytes);
		}
	}
	
	public function close(){
		if($this->handle instanceof Stream){
			return $this->handle->close();
		}else{
			return fclose($this->handle,$data,strlen($data));	
		}
	}
	public function write($data){
		if($this->handle instanceof Stream){
			return $this->handle->write($data,strlen($data));
		}else{
			return fwrite($this->handle,$data,strlen($data));	
		}
	}
	
	
	function seek($offset, $whence){
		if($this->handle instanceof Stream){
			return $this->handle->stream_seek($offset,$whence);
		}else{
			return fseek($this->handle,$offset,$whence);	
		}
		
    }

    function tell(){
    	if($this->handle instanceof Stream){
			return $this->handle->stream_tell() - strlen($this->current_content);
		}else{
			return ftell($this->handle) - strlen($this->current_content);
		}
    }

	function eof(){
    	if($this->handle instanceof Stream){
			return $this->handle->eof();
		}else{
			return feof($this->handle);	
		}
    }
}
