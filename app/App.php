<?
class App {

	public function __construct() {
		# Environments	
		if(file_exists(SERVER_APP_DIR.'/bootstrap.php')){
			include_once(SERVER_APP_DIR.'/bootstrap.php');
		}
	}

}
