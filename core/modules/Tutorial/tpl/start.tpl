<h2>Overall Directory Structure</h2>
<pre>
	/<b>app</b>			<- Your application module (if you have created it)
	/<b>core</b>			<- The core module
	/<b>doc</b>			<- Installation documentation
	/<b>location_handler</b>	<- The main entrypoint
	/<b>logs</b>			<- Various logs, http, error, other
</pre>

<h3>Module Locations</h3>
<p>
	There are 4 places for modules within the framework. Two of them are defined
	by the client (app and app/modules) and might not currently exist within your
	installation. Core and app are <b>base</b> modules. Here are the 4 places that modules
	can be found:
</p>
<pre>
	/<b>app</b>		<- An app IS a module
	/app/<b>modules</b>	<- And can contain submodules
	/<b>core</b>		<- The core IS a module
	/core/<b>modules</b>	<- And can contain submodules
</pre>

<h3>Module Directory Structure</h3>
<p>
	Modules are essentially packages of relevant information and logic.<br/>
	<dfn>NOTE:</dfn> <i>All classes (including cli scripts) must maintain the module
	namespace.</i>
</p>
<pre>
	./<b>Tutorial</b>		<- The basic Tutorial module under /core/modules/
	./Tutorial/<b>Tutorial.php</b> <- Where "class Tutorial" lives.
	./Tutorial/<b>tpl</b> 		<- Where basic templates (or partials) live.
	./Tutorial/<b>js</b>		<- Where javascript files live.
	./Tutorial/<b>css</b>		<- Where css files live.
	./Tutorial/<b>classes</b>	<- Classes that this module needs
	./Tutorial/<b>scripts</b>	<- Where cli scripts are
</pre>

<h2>Class Naming</h2>
<p>
	Everything has a place. Since there are so many different directories to put
	class files, there needs to be some convention to provide
	inherent structure. That convention is namespacing. Unforutnately,
	the version of php that this is built for, does not support namespaces.
	However, it does support <dfn>ghetto namespaces</dfn>.
</p>
<p>
	Ghetto namespaces are implemented using an underscore ('_') as a separator.
	For example, if I had a class underneath the Tutorial module that I wanted to
	call ScriptExample, I would name the class Tutorial_ScriptExample.
</p>
<pre>
	/app/classes/<b>MyFirstClass.php</b>			<- Holds "<b>class MyFirstClass</b>"
	/app/modules/Shop/classes/<b>Product.php</b>		<- Holds "<b>class Shop_Product</b>"
	/app/modules/Shop/classes/Product/<b>Soap.php</b>	<- Holds "<b>class Shop_Product_Soap</b>"
</pre>

<h2>Static Pages</h2>
<p>
	With Nth, static pages are possible, sensible, and simple to create, manage
	and maintain. Under the <b>base</b> modules ( core and app ), there exists a 
	www directory. When displaying these pages, you are underneath the scope of
	the Templator class and have access to the codebase.
</p>
<p>
	If the mimetype of a static file is "text/*", then the file is parsed by
	php, otheriwse, it is includeded with file_get_contents and is mtime cached.
</p>
<pre>
	/app/www/img/kittens.jpg	<- A static picture of a kitten!
	/core/www/index.php		<- A static php parsed page
</pre>
