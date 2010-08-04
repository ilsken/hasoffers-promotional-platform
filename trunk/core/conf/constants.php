<?php 
################################################################################
# Templator
################################################################################
########################################
# This should be defined if it is required by a view
########################################
# define('TEMPLATOR_NO_TEMPLATE',false);

################################################################################
# Server production level
################################################################################
# Different server levels
define('SERVER_LIVE',1);
define('SERVER_DEV',2);
define('SERVER_STAGING',5);

########################################
# This should be done in environment
########################################
# define('SERVER_ENVIRONMENT',SERVER_DEV);

################################################################################
# Server constants
################################################################################

# Paths
define('SERVER_DIR',
	($real_dir = realpath($path_dir = dirname(dirname(dirname(__FILE__)))))
	?$real_dir
	:$path_dir
);

# Special support for links
define('SERVER_APP_DIR',
	($real_dir = realpath($path_dir = SERVER_DIR.'/app'))
	?$real_dir
	:$path_dir
);
define('SERVER_EXT_DIR',
	($real_dir = realpath($path_dir = SERVER_APP_DIR.'/ext'))
	?$real_dir
	:$path_dir
);
define('SERVER_CORE_DIR',
	($real_dir = realpath($path_dir = SERVER_DIR.'/core'))
	?$real_dir
	:$path_dir
);

# Conf Dirs
define('SERVER_CONF_DIR',
	($real_dir = realpath($path_dir = SERVER_CORE_DIR.'/conf'))
	?$real_dir
	:$path_dir
);
define('SERVER_APP_CONF_DIR',
	($real_dir = realpath($path_dir = SERVER_APP_DIR.'/conf'))
	?$real_dir
	:$path_dir
);

########################################
# This should be done in environment
########################################
#define('SERVER_ENVIRONMENT',SERVER_DEV);
#define('SERVER_HOST',getenv('http_host'));
#define('SERVER_URL',$uri);

################################################################################
# Server caching
################################################################################
define('SERVER_CACHE_ETAG',1);
define('SERVER_CACHE_MTIME',2);

define('SERVER_CACHE_FILE',1);
define('SERVER_CACHE_SCRIPT',2);
define('SERVER_CACHE_ALL',SERVER_CACHE_FILE | SERVER_CACHE_SCRIPT);

########################################
# This should be done in environment
########################################
#define('SERVER_CACHE_TYPE',SERVER_CACHE_ETAG | SERVER_CACHE_MTIME);
#define('SERVER_CACHE_WHAT',SERVER_CACHE_ALL);
