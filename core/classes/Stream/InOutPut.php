<?php 
/**
 * This creates a duplex stream with an internal write bruffer (I have no idea
 * why)
 * @author carl
 * @package core-std
 */
class Stream_InOutPut extends Stream_Buffer{
	
	protected $read_stream;
	protected $write_stream;
	protected $write_buffer_size = 0;
	protected $buffer = '';
	
	public function __construct($write_buffer_size = 0,$input='php://input',$output='php://output'){
		$this->read_stream = fopen($input,'r');
		$this->write_stream = fopen($output,'w');
		$this->write_buffer_size = $write_buffer_size;
	}
	
	public function read($bytes){
		return fread($this->read_stream,$bytes);
	}
	
	public function write($data=null){
		if($this->write_buffer_size>0){
			$this->buffer .= $data;
			if(strlen($this->buffer) > $this->write_buffer_size){
				fwrite($this->write_stream,$this->buffer);
				$this->buffer = '';
			}
		}else{
			fwrite($this->write_stream,$data);
		}
	}
}
