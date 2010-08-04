<?php
if(!TUTORIAL_ENABLED){
	Server::find()->serveNotFound();
	exit;
}
/**
 * THis is the basic tutorial module.
 * @author carl
 * @package nth-example
 */
class Tutorial extends Templator{
	public static $route_okay = array(
		'index'
	);
	
	public function index(){
		return $this->display(
			$this->getTpl(
				'main'
				,array(
					'main'=>$this->getTpl('intro_main')
					,'menu'=>$this->getTpl('intro_menu')
				)
			)
		);
	}
}