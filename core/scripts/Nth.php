<?php
/**
 * This is the core script that is called for the CLI
 * @author carl
 * @package core
 */
class Nth extends Script{
	/**
	 * Lists installed scripts
	 */
	public function scripts($like=array(),$options=array()){
		$options = array_merge(array('modules-only' => false), $options);
		$like = array_shift($like);
		$this->write("Usage: nth script action [ARGS] [[--option=setting]]\n\n");
		$this->write("Available scripts:\n");
		$scripts = Framework::scriptList();
		foreach($scripts as $script){
			$script_file = Framework::classLocate($script,'scripts');
			$script_class = $script;
			require_once($script_file);
			if(self::isValidClass($script_class)){
				$this->write('  ' .$this->describeScript(new ReflectionClass($script_class)));
			}
		}
		$this->write("\n");
	}
	
	/**
	 * Locates a resource
	 */
	public function locate($actions,$options){
		$class = array_shift($actions);
		if($class == 'app'){
			$class_dir = SERVER_APP_DIR;
		}else if($class == 'core'){
			$class_dir = SERVER_CORE_DIR;
		}else{
			$class_file = Framework::classLocate($class);
			$class_dir = dirname($class_file);	
		}
		
		$this->write($class_dir."\n");
	}
	
	/**
	 * Various module interactions
	 * 	
	 * Actions:
	 *  list - Lists installed modules
	 *  list-remote - Lists available modules
	 *    --server=nthalk.com
	 *  install - installs a remote package
	 *    --dir=app/modules
	 *    --server=nthalk.com
	 *    --as-git-submodule
	 *    --as-git-repo
	 *	uninstall - removes a module
	 */
	public function modules($like=array(),$options=array()){
		$action = array_shift($like);
		if(isset($options['server'])){
			$server = $options['server'];
		}else{
			$server = 'nthalk.com';
		}
		if($action == 'uninstall'){
			$module = array_shift($like);
			$module_dir = Framework::moduleInstalled($module);
			if(!$module_dir){
				$this->write("Module does not appear to be installed: $module\n");
				return;
			}
			$this->write("Uninstalling: $module\n");
			
			$this->write("Running uninstall scripts...\n");
			if(method_exists($module,'uninstall')){
				if(!call_user_func(array($module,'uninstall'))||isset($options['force'])){
					$this->write("Uninstaller failed... Please inspect $module::uninstall() before completely removing or supply --force\n");
					return;
				}
			}
			
			$this->write("Removing submodule from app/.git cache...\n");
			$cmd = 'git rm --cached ' . $module_dir;
			//$this->write($cmd . "\n");
			$cmd_p = popen($cmd,'r');
			$this->write(fread($cmd_p,10000). "\n");
			
			$dir = SERVER_APP_DIR;
			$git_submodules_file = $dir .'/.gitmodules';
			if(file_exists($git_submodules_file)){
				$git_submodules = file_get_contents($git_submodules_file);
				$match = "/\[submodule.*$module\"\]\s+path.*\s+url.*\s?/";
				if(preg_match($match,$git_submodules)){
					$git_submodules_new = preg_replace($match,'',$git_submodules);
					$git_submodules_new  = preg_replace("/\n+/","\n",$git_submodules_new );
					$this->write("Removing submodule from submodules...\n");
					//$this->write($git_submodules_new . "\n");
					file_put_contents($git_submodules_file,$git_submodules_new);
				}
			}
			
			$git_conf_file = $dir . '/.git/config';
			if(file_exists($git_conf_file)){
				$git_conf = file_get_contents($git_conf_file);
				$match = "/\[submodule.*$module\"\](\s+path.*|\s+url.*)+?\s?/";
				if(preg_match($match,$git_conf)){
					$git_conf_new = preg_replace($match,'',$git_conf);
					$git_conf_new = preg_replace("/\n+/","\n",$git_conf_new);
					$this->write("Removing submodule from config...\n");
					//$this->write($git_conf_new . "\n");
					file_put_contents($git_conf_file,$git_conf_new);
				}
			}
			$this->write("Removing submodule from file system...\n");
			$cmd = 'rm -rf ' . $module_dir;
			//$this->write($cmd . "\n");
			$cmd_p = popen($cmd,'r');
			$this->write(fread($cmd_p,10000). "\n");
			
			$this->write("Done. It is up to you to commit this change into your app repo...\n");
			
		}else if($action == 'list'){
			$this->write("Currently installed modules:\n\n");
			foreach(Framework::moduleList(array_shift($like)) as $module){
				$this->write(
					' ' . $module . ' -' .$this->getDocumentation(new ReflectionClass($module))
				);
			}
			$this->write("\n");
		}else if($action =='list-remote'){
			$content = file_get_contents("http://$server/Packages");
			$packages = json_decode($content);
			if(!is_array($packages)){
				$this->write('Error retrieving data from module server: '.$server."\n");
			}else{
				foreach($packages as $package){
					$this->write($package->name . " - " . $package->version . "\n");
				}
			}
		}else if($action =='install'){
			if(realpath(getcwd()) !== SERVER_APP_DIR){
				$this->write("fatal: You must install modules from the root of your app dir ( ".SERVER_APP_DIR." )\n");
				return;
			}
			
			$content = file_get_contents("http://$server/Packages");
			$packages = json_decode($content);
			if(!is_array($packages)){
				$this->write('Error retrieving data from module server: '.$server."\n");
				exit -1;
			}else{
				$what = array_shift($like);
			}
			foreach($packages as $package){
				if($package->name == $what){
					// Define the dir
					$dir = SERVER_EXT_DIR;
					if(!file_exists($dir)){
						$dir_parts = explode('/',$dir);
						$curr_dir = '';
						do{
							$curr_dir .= '/' . array_shift($dir_parts);
							if(!file_exists($curr_dir)){
								mkdir($curr_dir);
							}
						}while(!file_exists($dir));
					}
					
					// If app is git repo
					$app_is_git = file_exists(SERVER_APP_DIR.'/.git');
					// Install directory
					$dir .= '/modules/';
					if(!file_exists($dir)){
						mkdir($dir);
					}
					$dir .= $package->name;
					$dir = substr($dir,strlen(SERVER_APP_DIR)+1);
					if(isset($options['as-git-submodule'])){
						if(!$app_is_git){
							$this->write('fatal: app is not a git repo. Please run `git init` within '.SERVER_APP_DIR."\n");
							exit -1;
						}
						$cmd = 'git submodule add ' . $package->git_url . ' ' .$dir;
					}else{
						$cmd = 'git clone --depth 0 ' . $package->git_url . ' ' . $dir;
					}
					
					$this->write("Retrieving package $what...\t");
					$this->write("\ncmd: $cmd\n");
					
					if(shell_exec($cmd)){
						if(!isset($options['as-git-repo'])&&!isset($options['as-git-submodule'])){
							if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
						    exec( "rd /s $dir/.git" );
						  }else{
						  	if(!$app_is_git){
									$this->write('warning: app is not a git repo. Installing modules this way may pollute the framework and prevent you from having your application as a standalone repository. Please run `git init` within app to avoid this warning.'."\n");
								}
						    exec( "rm -rf $dir/.git" );
						  }
						}
						$this->write("[Success]\n");
						
						// If the module has a configuration function, run that on install
						if(method_exists($what,'setup')){
							call_user_func_array(array($what,'setup'),array($this->out_stream,$this->in_stream));
						}
						
						$this->write("Package installed!\n");
						exit;
					}else{
						exit -1;
					}
				}
			}
			
			$this->write('Package: ' . json_encode($what) . ' not found.' . "\n");
		}else{
			$this->write("Usage: nth action [ARGS] [[--option=setting]]\n\n");
			$this->write($this->getDocumentation(new ReflectionMethod($this,__FUNCTION__),true));
		}
	}
}