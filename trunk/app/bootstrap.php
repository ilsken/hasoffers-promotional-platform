<?

if(file_exists(SERVER_APP_CONF_DIR.'/local-configuration.php')){
	include_once(SERVER_APP_CONF_DIR.'/local-configuration.php');
}

$debug_level = Conf::read('DEBUG');
if ( Conf::read('DEBUG') > 0 ) {
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
} else {

}
