<?php
/**
 * This is for renaming classes. It is alpha. Beware.
 * @author carl
 * @package core
 */
class Refactor extends Script{
	public function find($actions,$options=array()){
		
		$lineno = isset($options['line']);
		$no_match = isset($options['no-match']);
		$count = isset($options['count']);
		$return = isset($options['return']);
		$color = !isset($options['no-color']);
		
		$search = array_shift($actions);
		if($search){
			$cmd = 'grep '
				. ($color?'--color=always ':'')
				. '-r' 
				. ($no_match?'l':'')
				. ($lineno?'n':'')
				. ' -E ' . escapeshellarg($search) 
				. ' ' .SERVER_DIR . '* '
				. ' | grep -v .git'
				. ($count?' | wc -l':'')
			;
			$this->write("\nCmd: ".$cmd."\n");
			$out = popen($cmd,'r');
			$ret = '';
			while($content = fread($out,255)){
				if($return){
					$ret .= $content;
				}else{
					$this->write($content);
				}
			}
			return $ret;
		}else{
			$this->write('Usage: nth Refactor find Refactor'."\n");
		}
	}
	
	public function classRename($actions,$options){
		$from = array_shift($actions);
		$to = array_shift($actions);
		
		if(!$from || !$to){
			$this->write('Please supply a from and to class.' . "\n");
			return;
		}
		$this->write('Class: ' .$from . "\n");
		
		$this->write('Referenced lines: ');
		$from_class_references = '[ \n	]'.$from.'[(: 	{\$]';
		$this->find(array($from_class_references),array('count'=>true));
		$this->write('See Matches? (Y/n)');
		$answer = $this->read(2);
		if($answer == "\n" || strtolower($answer) == "y\n"){
			$this->write("-----------------------------------------------\n");
			$this->find(array($from_class_references));
			$this->write("-----------------------------------------------\n");
		}
		
		$this->write('To: ' . $to . "\n");
		$this->write('Referenced lines: ');
		$to_class_references = '[ \n\t]'.$to.'[(: {\$]';
		$this->find(array($to_class_references),array('count'=>true));
		$this->write('See Matches? (Y/n)');
		$answer = $this->read(2);
		if($answer == "\n" || strtolower($answer) == "y\n"){
			$this->write("-----------------------------------------------\n");
			$this->find(array($to_class_references));
			$this->write("-----------------------------------------------\n");
		}
		
		$this->write('Class name collisions: ');
		$to_class_finder = "class +".$to."[ {]+";
		$this->find(array($to_class_finder));
		$this->write("\n");
		
		$this->write('Procede? (Y/n)');
		$answer = $this->read(2);
		if($answer == "\n" || strtolower($answer) == "y\n"){
			
			$locations = array(
				SERVER_APP_DIR . 'classes/'
				,SERVER_APP_DIR . 'modules/'
				,SERVER_EXT_DIR . 'classes/'
				,SERVER_EXT_DIR . 'modules/'
				,SERVER_CORE_DIR . 'classes/'
				,SERVER_CORE_DIR . 'modules/'
			);
			foreach($locations as $k=>$location){
				if($k%2==1){
					$dird_to = explode('_',$to);
					$module = array_shift($dird_to);
					array_unshift($dird_to,'classes');
					array_unshift($dird_to,$module);
					$dird_to = implode('/',$dird_to);
				}else{
					$dird_to = str_replace('_','/',$to);
				}
				$locations[$k] = $location . $dird_to . '.php'; 
			}
			do{
				$this->write("Class destination: \n");
				foreach($locations as $k=>$location){				
					$this->write(
						"\t$k) "
						. substr($location,strlen(SERVER_DIR))
						. "\n"
					);
				}
				$this->write("Choose: [0-5]");
				$answer = $this->read(2);
			}while(!preg_match('/^[0-5]\n/',$answer));
			
			$destination = $locations[$answer{0}];
			$from_class_finder = "class[ \t]+".$from."[ \t{]+";
			$source = $this->find(array($from_class_finder),array('no-match'=>true,'return'=>true,'no-color'=>true));
			$source = substr($source,0,-1);
			
			if($source !== $destination){
				$this->write("Moving: $source\nTo: $destination\n");
				if(file_exists($destination)){
					$this->write("Destination file: $destination Exists!\n");
					die();
				}
				if(!file_exists(dirname($destination))){
					mkdir(dirname($destination),0777,true);
				}
				if(!file_exists($source)){
					$this->write("Source file: $source Does not exist!\n");
					die();
				}
				copy($source,$destination);
				unlink($source);
			}
			$cmd = 'grep -rl -E ' . escapeshellarg($from_class_references) . ' ' . SERVER_DIR . '* ';
			
			$stream = popen($cmd,'r');
			$files = '';
			while($content = fread($stream,255)){
				$files .= $content;
			}
			$files = explode("\n",$files);
			foreach($files as $file){
				if($file){
					$from_class_references_match = '\([ \n\t]\)'.$from.'\([(: {\n]\)';
					$cmd = "sed --in-place -e 's/$from_class_references_match/\\1$to\\2/g' " . $file;
					popen($cmd,'r');
				}
			}
		}
	}
} 
