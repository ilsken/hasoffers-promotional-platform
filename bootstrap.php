<?php
/**
 * This file bootstraps the fundamental elements of this framework:
 *   1. Loads constants
 *   2. Loads environment variables
 *   3. Sets up autoloading
 *   4. Sets default error handler
 *
 * If you wish to bootstrap your application, create an App class
 * at the base of your app directory and put your bootstrap logic
 * in the class contructor.
 */

# Constants
require_once(dirname(__FILE__).'/core/conf/constants.php');
if(file_exists(SERVER_APP_CONF_DIR.'/constants.php')){
	include_once(SERVER_APP_CONF_DIR.'/constants.php');
}

# The Framework 
# This loads the framework for various things
require_once(SERVER_DIR .'/core/classes/Framework.php');

# Environments
if(file_exists(SERVER_APP_CONF_DIR.'/environment.php')){
	include_once(SERVER_APP_CONF_DIR.'/environment.php');
}
include_once(SERVER_CONF_DIR.'/environment.php');

function __autoload_modules($class) {
	$class_file = Framework::classLocate($class);	
	if(file_exists($class_file)){
		require_once($class_file);
	}
}
spl_autoload_register(null, false);
spl_autoload_register('__autoload_modules');

# Error Handler
$eh = new ErrorHandler(ERROR_LEVEL);
set_error_handler(array($eh,'handleErrorAsException'));
set_exception_handler(array($eh,'handleException'));

# App Bootstrapping
if (file_exists(SERVER_APP_DIR . '/App.php')) {
	require_once(SERVER_APP_DIR . '/App.php');
}
if(class_exists('App', true)) {
	new App();
}
