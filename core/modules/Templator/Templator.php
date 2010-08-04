<?php
/**
 * The Templator class is intended to be used to build web pages off of. It 
 * knows where js, css, tpl files are and how to include them and reference them
 * across urls.
 * 
 * The Templator also has some handy streaming functions and some handy
 * recording functions for complicated temlating.
 * 
 * @author carl
 * @package core
 */
class Templator extends Module{
	############################################################################
	# Statics
	############################################################################
	public static $route_okay = array(
		'css'
		,'js'
		,'404'=>'notFound'
	);
	
	/*
	 * This array should contain a list of elements you wish to
	 * load via loadElements()
	 */
	public $elements = array();
	
	/**
	 * This renders the CSS and JS header for the required CSS and JS files.
	 * This call should probably be somewhere in your main template, unless
	 * you wish to handle js and css requirements yourself. 
	 * @return string the CSS and JS header
	 */
	public static function getRequiredResourceHeader(){
		$out = '';
		foreach(self::$required_js as $js){
			$out .= '<script src="'.$js.'"></script>'."\n";
		}
		foreach(self::$required_css as $css){
			$out .= '<link rel="stylesheet" type="text/css" href="'.$css.'"/>'."\n";
		}
		return $out;
	}
	
	/**
	 * This is a list of the required js files
	 * @var string[] script paths
	 */
	protected static $required_js = array();
	
	/**
	 * This is a list of the required css files 
	 * @var string[] css paths
	 */
	protected static $required_css = array();
	
	################################################################################
	# Member variables
	################################################################################
	/**
	 * The content to be displayed within the template
	 * @var string
	 */
	protected $content;
	
	/**
	 * The template that the content is to be embedded in
	 * @var string
	 */
	protected $template = 'plain';
	
	/**
	 * The html title bar title.
	 * @var string
	 */
	protected $title;
	
	/**
	 * The HTML title bar additional content
	 * @var string
	 */
	protected $title_content;
	
	/**
	 * The footer that is captured during streaming output. 
	 * @var string
	 */
	private $footer = null;
	
	/**
	 * How many times we have used the ->streamCreate function.
	 * @var int
	 */
	private $stream_depth = 0;
	
	/**
	 * Current recordings
	 * @var array
	 */
	private $recordings = array();
	
	################################################################################
	# Member functions
	################################################################################
	
	######################################
	# Routes
	######################################
	/**
	 * This is what is run when something is not found, or should not be
	 * found.
	 */
	public function notFound(){
		$this->template = '404';
		$this->display();
	}

	/**
	 * This is the route function that gives up the css
	 * @return string The css contents
	 */
	public function css(){
		header('Content-Type: text/css',true,200);
		$args = func_get_args();
		return $this->getCss(implode('/',$args));
	}
	
	/**
	 * This is the routed function that retrieves the js content
	 * @return string The js contents
	 */
	public function js(){
		header('Content-Type: text/javascript',true,200);
		$args = func_get_args();
		return $this->getJs(implode('/',$args));
	}
	
	######################################
	# Display
	######################################
	/**
	 * Sets content and displays
	 * @param string $content
	 */
	public function display($content=null){
		if($content){
			$this->setContent($content);
		}
		echo $this->getDisplay();
	}
		
	/**
	 * Returns the content, or, if streaming, shows what content we have added
	 * and saves the footer for later.
	 * @return unknown_type
	 */
	public function showContent(){
		if (isset($this->authRequired) && $this->authRequired == true){
			if (AuthUser::hasAuth()){echo $this->content;}
			else {
				Server::redirect('/Home/login?r=' . $_SERVER['REQUEST_URI']);
				
			}
		}
		else {echo $this->content;}
		
		if($this->stream_depth > 0){
			ob_flush();
			flush();
			// Record the footer
			ob_start();
		}
	}
	
	/**
	 * Returns the display, either the content, or the template.
	 * @return string
	 */
	public function getDisplay(){
		if(!defined('TEMPLATOR_NO_TEMPLATE')){
			return $this->getTpl($this->template);
		}else{
			return $this->showContent();
		}
	}
	
	######################################
	# Sections
	######################################
	/**
	 * Sets the html title bar title
	 * @param string $title
	 */
	public function setTitle($title){
		$this->title = $title;
	}
	
	/**
	 * Sets the html title bar additional content
	 * @param string $title_content;
	 */
	public function setTitleContent($title_content){
		$this->title_content = $title_content;
	}
	
	/**
	 * Gets the html title bar title
	 * @return string
	 */
	public function getTitle(){
		return $this->title;
	}
	
	/**
	 * Gets the additional HTML content for the title
	 * @return string
	 */
	public function getTitleContent(){
		return $this->title_content;
	}
	

	/**
	 * Sets the main template
	 * @param string $template
	 */
	public function setTemplate($template) {
		$this->template = $template;
	}
	
	/**
	 * Sets the content
	 * @param string $content
	 */
	public function setContent($content){
		$this->content = $content;
	}
	
	/**
	 * Sets a css resource to be referenced when the getRequiredResourceHeader
	 * is called.
	 * @param string $css file
	 */
	public function requireCss($css){
		$css_url = $this->getUrlForCss($css);
		if(!in_array($css_url, self::$required_css)){
			 self::$required_css[] = $css_url;
		}
	}
	
	/**
	 * Sets a js resource to be referenced when the getRequiredResourceHeader
	 * is called.
	 * @param string $js file
	 */
	public function requireJs($js){
		$js_url = $this->getUrlForJs($js);
		if(!in_array($js_url, self::$required_js)){
			 self::$required_js[] = $js_url;
		}
	}

	######################################
	# Streaming
	######################################
	/**
	 * Streams a chunk of text. This causes the template to be pushed out if it
	 * is the first stream attempt. The footer is cached until streamEnd is
	 * called.
	 * @param string $content
	 * @param bool $and_flush
	 */
	public function stream($content,$and_flush=true){
		if($this->stream_depth == 0){
			$this->stream_depth = 1;
			// Turn off services that may inhibit streaming
			@apache_setenv('no-gzip', 1);
	    	@ini_set('zlib.output_compression', 0);
	    	@ini_set('implicit_flush', 1);
			// This echos out the header
			$this->display();
			// Because we started the output buffer
			// in the getContent() function, we now have the footer
			// in it.
			$this->footer = ob_get_clean();
		}
		
		if($and_flush){
			echo str_pad($content,4096);
			flush();
		}else{
			echo $content;
		}
	}
	
	/**
	 * Starts a stream
	 * @param string $content
	 * @param bool $and_flush
	 */
	public function streamStart($content,$and_flush=true){
		if($this->stream_depth > 0){
			$this->stream_depth++;
		}
		return $this->stream($content,$and_flush);
	}
	
	/**
	 * Ends the stream by outputting the footer.
	 * @return unknown_type
	 */
	public function streamEnd(){
		$this->stream_depth--;
		if($this->stream_depth >= 0){
			echo $this->footer;			
		}
	}
	
	######################################
	# Buffering
	######################################
	/**
	 * Starts an output buffer for the following var name under
	 * $this->recordings[$var_name]
	 * 
	 * @param $var_name The name of the recording
	 * @return null
	 */
	public function record($var_name=false){
		ob_start();
		$this->recordings[] = $var_name;
	}
	
	/**
	 * Writes out a recording
	 * @return string
	 */
	public function write(){
		$var_name = array_pop($this->recordings);
		$ret = ob_get_clean();
		if($var_name){
			$this->$var_name = ob_get_clean();
		}
		return $ret;
	}
		
	######################################
	# File getters/Locaters
	######################################
	/**
	 * Retrieves a tpl within MODULE_CLASS_DIR/tpl/$tpl and returns the content.
	 * Data is extracted into scope
	 * @param string $tpl The tpl file name relative to the MODULE_CLASS_DIR
	 * @param array $data The data to be extracted into scope 
	 * @return string
	 */
	public function getTpl($tpl,$data=array()){
		return $this->getInclude("/tpl/$tpl.tpl",$data);
	}
	
	/**
	 * Retrieves the contents of a relative module js file
	 * @param string $js_file
	 * @return string The contents of the local js file
	 */
	public function getJs($js_file){
		$file = $this->locateInclude('/js/'.$js_file);
		Server::find()->setCacheMtime(filemtime($file));
		return $this->getInclude($file);
	}
	
	/**
	 * Retrieves the contents of a relative module css file
	 * @param string $css_file
	 * @return string The contents of the local css file
	 */
	public function getCss($css_file){
		$file = $this->locateInclude('/css/'.$css_file);
		Server::find()->setCacheMtime(filemtime($file));
		return $this->getInclude($file);
	}

	/**
	 * Retrieves a xml within MODULE_CLASS_DIR/xml/$xml and returns the content.
	 * Data is extracted into scope
	 * @param string $xml The xml file name relative to the MODULE_CLASS_DIR
	 * @param array $data The data to be extracted into scope 
	 * @return string
	 */	
	public function getXml($xml, $data = array()){
		return $this->getInclude("/xml/$xml.xml", $data);
	}
	
	/**
	 * Locates a js resource for this module
	 * @param string $js The file
	 * @return string the path of the file
	 */
	public function getUrlForJs($js){
		if(file_exists($this->path . '/js/' . $js . '.js')){
			return '/' . $this->class . '/js/' . $js . '.js';
		} else if(file_exists(SERVER_APP_DIR . '/www/js/' . $js . '.js')){
			return '/js/' . $js . '.js'; 
		} else {
			return '/Templator/js/' . $js . '.js';
		}
	}
	
	/**
	 * Locates a js resource for this module
	 * @param string $js The file
	 * @return string the path of the file
	 */
	public function getUrlForCss($css){
		if(file_exists($this->path . '/css/' . $css . '.css')){
			return '/' . $this->class . '/css/' . $css . '.css';
		} else if(file_exists(SERVER_APP_DIR . '/www/css/' . $css . '.css')){
			return '/css/' . $css . '.css'; 
		} else{
			return '/Templator/css/' . $css . '.css';
		}
	}
	
	/**
	 * Load all elements in $this->elements for
	 * use in the main display
	 */
	public function getElements(){
		foreach ($this->elements as $element){
			$this->elements[$element] = $this->getTpl('elements/' . $element);
		}
	}
	
	/**
	 * Option generator, queries enum columns for their types
	 * and generates a list of options
	 */
	function getOptions($database, $table, $column, $selected_value = ""){
		$slave = App_Database::findRead();
		$data = $slave->query_assoc("SHOW COLUMNS FROM `$database`.`$table` LIKE '$column'");
		$types = explode(",", str_replace(array("enum(", "'",")"),"",$data['Type']));
		$return = "";
		foreach ($types as $type){
		    if($type == $selected_value){$selected = 'selected="selected"';}
		    else {$selected = "";}
		    $return .= "<option value='$type' $selected>" . ucwords(str_replace("_", " ", $type)) . "</option>\n";
		}
		return $return;
	}
	
	/**
	 * 
	 */
	function isActivePage($page_name, $class = 'current_page'){
		if  ($page_name == get_class($this)){return " class='$class' ";}
	}
	
	public function generateTableRows($rows = array(), $type){
		$odd = "odd";
		foreach ($rows as $row){
		    if ($odd == "even"){$odd = "odd";} else {$odd = "even";}
		    $this->rows .= $this->getTpl('elements/' . $type . 's_row', array($type => $row, 'odd' => $odd));
		}
		return $this->rows;      		
	}	
	
}
