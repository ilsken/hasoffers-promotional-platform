<?php 
/**
 * This is the basic filter classes that extend this should simply overwrite
 * the scan, mutateOut and mutateIn functions.
 * @author carl
 * @package core-std
 */
class Stream_Filter extends Stream{
	protected $current_content = '';
	
	public function __construct($handle=null){
		parent::__construct($handle);
		$this->current_content = '';
	}
	
	public function scan($content,&$misc=null){
		return $content;
	}
	public function mutateOut($content){
		return $content;
	}
	public function mutateIn($content,$misc){
		return $content;
	}
	
	public function read($bytes,&$bytes_skipped=null){
		if($bytes !== 0){
			$this->current_content .= parent::read($bytes,$bytes_skipped);
		}
 
		$misc = null;
		$matching_content = $this->scan($this->current_content,$misc);
		
		if($matching_content){
			$found_pos = strpos($this->current_content,$matching_content);
			$bytes_skipped = substr($this->current_content,0,$found_pos);
			$this->current_content = substr($this->current_content, $found_pos+strlen($matching_content)); 
			return $this->mutateIn($matching_content,$misc);
		}else{
			if(parent::eof()){
				$this->current_content = '';
			}
			return null;
		}
	}
	
	public function write($content){
		$modified = $this->mutateOut($content);
		return parent::write($modified);
	}
	
	function eof(){
    	if(strlen($this->current_content) > 0){
    		return false;
    	}else{
    		return parent::eof();
    	}
	}
}
