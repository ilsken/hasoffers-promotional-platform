<h2>Configuration and Environments</h2>
<p>
	There are two types of configuration files loaded by default in this framework,
	Constants and environment. Constats files should simply define constants that
	are either always constant or used in the environment configuration files.
	Environment configuration files contain conditional constant definition.
</p>
<p>
	The purpose of the constants and environment files is to let the app
	or loading script overlay the core or app's environment. For instance, if you
	wanted to change your hostname on the app level and not on the core level, you
	can by defining it in the app's environment.
</p>
<p>
	By default, the location_handler/index.php includeds the following <b><em>IN ORDER</em></b>:
</p>
<pre>
	/app/conf/<b>constants.php</b>		<- App <em>non overridable</em> constants
	/app/conf/<b>environment.php</b>	<- App <em>overridable</em> constants
	/core/conf/<b>constants.php</b>	<- Core <em>non overridable</em> constants
	/core/conf/<b>environment.php</b>	<- Core <em>overridable</em> constants
</pre>

<h3>Constants Files</h3>
<p>
	These are hard coded settings that are not overridable.
</p>
<p>Example:</p>
<pre>
	define('SERVER_LIVE',1);
	define('SERVER_DEV',2);
	define('SERVER_STAGING',5);
</pre>

<h3>Environment Files</h3>
<p>
	These are conditionally hardcoded settings that are once set.
</p>
<p>Example:</p>
<pre>
	# The environment
	if(!defined('SERVER_ENVIRONMENT')){
		define('SERVER_ENVIRONMENT',SERVER_DEV);
	}
	
	# Hostname
	if(!defined('SERVER_HOST')){
		define('SERVER_HOST',getenv('http_host'));
	}
</pre>