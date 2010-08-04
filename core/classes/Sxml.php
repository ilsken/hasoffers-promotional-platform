<?php
/**
 * This is the basic Sxml streaming class
 * @author carl
 * @package core-std
 */
class Sxml{
	protected $rendered_head = false;
	protected $attributes = array();
	protected $type = 'div';
	protected $closed = false;
	protected $children = array();
	protected $stream = null;
	
	public function setAttribute($name,$value){
		if($this->rendered_head){
			throw new Exception("Setting of Sxml attributes not allowed, header has already been rendered.");
		}else{
			$this->attributes[$name] = $value;
		}
	}
	
	public function setAttributes(array $attributes = array()){
		if($this->rendered_head){
			throw new Exception("Setting of Sxml attributes not allowed, header has already been rendered.");
		}else{
			$this->attributes = $attributes;
		}
	}
	
	public function setType($type = 'div'){
		if($this->rendered_head){
			throw new Exception("Setting of Sxml type not allowed, header has already been rendered.");
		}else{
			$this->type = $type;
		}
	}
	
	public function getType(){
		return $this->type;
	}
	
	public function __construct($type,array $attributes = array(), $content = null, Stream_Filter_Sxml $stream=null){
		$this->setAttributes($attributes);
		$this->type = $type;
		$this->stream = $stream;
		
		if($content){
			if(is_array($content)){
				foreach($content as $item){
					$this->addChild($item);
				}
			}else{
				$this->addChild($content);
			}
		}
		
		if(!$stream){
			$this->close();
		}
	}
	public function renderHead($and_close = false){
		$this->rendered_head = true;
		return '<'.$this->type.$this->renderAttributes().($and_close?'/':'').'>';
	}
	public function __toString(){
		return $this->render();
	}
	public function write($destroy=false){
		$this->stream->write($this->render($destroy));
	}
	public function render($consume_children=false){
		if(!$this->getChild(0)){
			return $this->renderHead(true);	
		}else{
			$head = $this->renderHead();
			$foot = $this->renderFoot();
			$body = $this->renderBody($consume_children);
			return $head.$body.$foot;
		}
	}
	public function renderBody($destroy=false){
		$out = '';
		while($child = $this->consumeChild()){
			if($child instanceof Sxml){
				$out .= $child->render($destroy);
			}else{
				$out .= $child;
			}
		}
		return $out;
	}
	public function populateChildren($index=null){
		if(!is_null($index)){
			while(!$this->closed && $this->stream->read(255));
		}else{
			while(!$this->closed && $this->stream->read(255) && count($this->children) < $index);	
		}
	}
	public function getChild($index){
		if(!isset($this->children[$index])){
			$this->populateChildren($index);
		}
		if(isset($this->children[$index])){
			return $this->children[$index];
		}
	}
	public function consumeChild(){
		$child = array_shift($this->children);
		if(!$child && !$this->closed){
			$this->stream->read(255);
			$child = array_shift($this->children);
		}
		return $child;
	}
	public function renderFoot(){
		return '</'.$this->type.'>';
	}
	public function addChild($child){
		array_push($this->children,$child);
		return $child;
	}
	public function renderAttributes(){
		$out = ' ';
		foreach($this->attributes as $k=>$v){
			if(!is_null($v)){
				$out .= ' '.$k.'="'.addslashes($v).'"';
			}else{
				$out .= ' '.$k;
			}
		}
		return substr($out,1);
	}
	public function __destruct(){
		$this->children = array();
	}
	public function close(){
		$this->closed = true;
	}
} 
