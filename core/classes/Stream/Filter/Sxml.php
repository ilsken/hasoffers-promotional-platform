<?php
/**
 * This is the Streaming XML filter that takes a stream and can iterate
 * across elements.
 * @author carl
 * @package core-std
 */
class Stream_Filter_Sxml extends Stream_Filter_Regexp{
	protected $open_elements = array();
	
	public function __construct($handle,$types = array()){
		if(!$types){
			$types = array('[^>\/ ]*');
		}
		if(!is_array($types)){
			$types = array($types);
		}
		$types_regexp = '';
		foreach($types as $type){
			$types_regexp .=$type . '|';
		}
		
		if($types_regexp){
			$types_regexp = '('.substr($types_regexp,0,-1).')';
		}
		
		$regexp = '/(?:<(\/)?(\s*[^\>\s]+)([^>]*)>|([^<]+))/';
		parent::__construct($handle,$regexp);
	}
	
	public function addTextNode($text){
		$sxml = $this->open_elements[count($this->open_elements)-1];
		$sxml->addChild($text);
	}
	
	public function addElement($element_type,$attributes){
		$sxml = new Sxml($element_type,$attributes,null,$this);
				
		if(isset($this->open_elements[count($this->open_elements)-1])){
			$sxml_parent = $this->open_elements[count($this->open_elements)-1];
			$sxml_parent->addChild($sxml);
		}
		
		array_push($this->open_elements,$sxml);
		return $sxml;
	}
	
	public function closeElement($type){
		$sxml = array_pop($this->open_elements);
		$sxml->close();
	}
	
	public function parseAttributes($text){
		$attributes = array();
		preg_match_all(
			'/(?:\s([^\s=]+)=?(?:"(.*)")?(?:\'(.*)\')?([^>\s]*))/'
			,$text
			,$attribute_matches
		);
		
		if(isset($attribute_matches[1])){
			foreach($attribute_matches[1] as $k=>$match){
				if($attribute_matches[2][$k]){
					$attributes[$match] = '"'.$attribute_matches[2][$k].'"';	
				}else if($attribute_matches[3][$k]){
					$attributes[$match] = "'".$attribute_matches[3][$k]."'";
				}else if($attribute_matches[4][$k]){
					$attributes[$match] = $attribute_matches[4][$k];
				}else{
					$attributes[$match] = null;
				} 
			}
		}
		return $attributes;
	}
	
	public function mutateIn($content,$matches){
		if(isset($matches[4])){
			$this->addTextNode($matches[4]);
			return $this->read(0);
		}else{
			$is_closing = $matches[1];
			$element_type = $matches[2];
			
			if($is_closing){
				$this->closeElement($element_type);
				return $this->read(0);
			}else{
				if(isset($matches[3])){
					$attributes = $this->parseAttributes($matches[3]);
				}else{
					$attributes = array();
				}
				if(substr($element_type,-1,1) == '/'){
					$element_type = substr($element_type,0,-1); 
					$this->addElement($element_type,$attributes);
					$this->closeElement($element_type);
					return $this->read(0);
				}else{
					return $this->addElement($element_type,$attributes);
				}
			}
		}
	}
}
