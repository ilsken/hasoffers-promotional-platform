<?
ini_set('display_errors', true);
if(!defined('SERVER_CACHE_TYPE')){
	define('SERVER_CACHE_TYPE',0);
}
if(!defined('SERVER_CACHE_WHAT')){
	define('SERVER_CACHE_WHAT',0);
}

date_default_timezone_set('America/Los_Angeles');

define('SERVER_DEFAULT_ROUTE_CLASS','Main');