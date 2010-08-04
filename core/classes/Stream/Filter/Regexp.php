<?php
/**
 * This is the regular expression stream filter. Given a regular expression
 * string.
 * @author carl
 * @package core-std
 */
class Stream_Filter_Regexp extends Stream_Filter{
	protected $regexp;
	public function __construct($handle,$regexp){
		parent::__construct($handle);
		$this->regexp = $regexp;
	}
	
	public function scan($content,&$misc=null){
		preg_match($this->regexp,$content,$misc);
		if(isset($misc[0])){
			return $misc[0];
		}else{
			return null;
		}
	}
	
	public function mutateIn($content,$misc){
		if($misc){
			return $misc;
		}else{
			return $content;
		}
	}
}
