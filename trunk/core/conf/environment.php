<?php 
################################################################################
# Server Environment
################################################################################
# Default route moulde
if(!defined('SERVER_DEFAULT_ROUTE_CLASS')){
	define('SERVER_DEFAULT_ROUTE_CLASS','Tutorial');
}

# This is the base url that the framework will repond to.
if(!defined('SERVER_BASE_URL')){
	define('SERVER_BASE_URL','');
}

# Additional root locations
# (separated by colon ':')
if (!defined('AUTOLOAD_PATH')) {
	define('AUTOLOAD_PATH', '');
}

# Class namespaces using in the app classes directory
# (separated by colon ':')
if (!defined('APP_CLASS_NAMESPACES')) {
	define('APP_CLASS_NAMESPACES', '');
}

# The environment
if(!defined('SERVER_ENVIRONMENT')){
	define('SERVER_ENVIRONMENT',SERVER_DEV);
}

# Hostname
if(!defined('SERVER_HOST')){
	define('SERVER_HOST',getenv('http_host'));
}

# Request URL
if(!defined('SERVER_URL')){
	$uri = getenv('REQUEST_URI');
	define('SERVER_URL',$uri);
}

# Tutorial
if(!defined('TUTORIAL_ENABLED')){
	define('TUTORIAL_ENABLED',true);
}

# Caching
if(!defined('SERVER_CACHE_TYPE')){
	define('SERVER_CACHE_TYPE',SERVER_CACHE_ALL);
}
if(!defined('SERVER_CACHE_WHAT')){
	define('SERVER_CACHE_WHAT',SERVER_CACHE_ALL);
}

if(!defined('ERROR_LEVEL')){
	define('ERROR_LEVEL',E_ALL);
}

if(!defined('ERROR_DISPLAY_MODE')){
	define('ERROR_DISPLAY_MODE','html');
}
