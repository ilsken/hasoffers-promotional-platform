<?php
/**
 * This is the error handler that is invoked to handle every error.
 * Note that every error is default to be an exception! =)
 * @author carl
 * @package core
 */
class ErrorHandler{
	public function handleException(Exception $exception){
		$msg = "Uncaught Exception: " . $exception->getMessage(). "\n";
		$msg .= $exception->getMessage()."\n";
		$msg .= 'Line: ' . $exception->getLine() . " in " . $exception->getFile();
		$msg .= "\n\nTrace Summary:\n" . self::traceFormat($exception->getTrace());
		if(ERROR_DISPLAY_MODE == 'html'){
			$msg .= "\nFull Trace: \n".print_r($exception->getTrace(), 1);
		}
		$this->show(
		$msg,
		$exception->getFile(),
		$exception->getLine(),
		$exception->getCode(),
			'E_WARNING'
			);
	}
	public function handleErrorAsException($errno, $errstr, $errfile, $errline){
		if(error_reporting() == 0) {// if it was called with @ in front of it, don't do anything
			return true;
		}
		throw new ErrorException($errstr,$errno,$errno,$errfile, $errline);
	}
	public function handleError($errno, $errstr, $errfile, $errline){
		if(error_reporting() == 0) {// if it was called with @ in front of it, don't do anything
			return true;
		}
		switch($errno) {
			case E_PARSE:
				$msg  = "Error [$errno]: $errstr\n";
				$msg .= "Fatal line $errline in file $errfile\n";

				$level = 'E_PARSE';
				break;

			case E_WARNING:
				$msg = "Warning: [$errno] $errstr in $errfile line $errline\n";
				$level = 'E_WARNING';
				break;

			case E_NOTICE:
				$msg = "Notice: [$errno] $errstr in $errfile line $errline\n";
				$level = 'E_NOTICE';
				break;

			default:
				if (in_array($errno, array(2048) )) {// ignore depricated errors
					return true;
				}
				$msg =  "Unknown Error Type: [$errno] $errstr in $errfile line $errline\n";
				$level = 'OTHER';
				break;

		}
		$this->show(
		$msg,
		$errfile,
		$errline,
		$errno
		);
	}
	protected function log($msg,$file,$line,$code){

	}
	protected function show($msg,$file,$line,$code){
		if (Conf::Read('debug') > 0){
			if(ERROR_DISPLAY_MODE == 'html'){
				echo "<pre>$msg</pre>";
			}else{
				echo $msg;
			}
		}
	}
	public static function traceBack($level=2,$trace=array()){
		if(!$trace){
			$trace = debug_backtrace();
		}
		for($i = 0; $i< $level;$i++){
			array_shift($trace);
		}
		return self::traceFormat($trace);
	}
	public static function traceFormat($trace){
		try{
			$file_matches = null;
			$max_file_length = 0;
			foreach($trace as $index=>$call){
				if(isset($call['file'])){
					$file_length = strlen($call['file']);
					if( $file_length > $max_file_length){
						$max_file_length = $file_length;
					}

					if(is_null($file_matches)){
						$file_matches = $call['file'];
					}else{
						$file = $call['file'];
						$str_match = '';
						for(
							$file_index=0;
							$file_index<strlen($file_matches)
							&&$file_index<strlen($file);
							$file_index++
						){
							$char = @$file_matches[$file_index];
							if($char == $file[$file_index]){
								$str_match .= $char;
							}else{
								break;
							}
						}
						$file_matches = $str_match;
					}
				}
			}

			ob_start();
			if($file_matches){
				echo "Root: $file_matches\n";
			}
			foreach($trace as $index=>$call){
				if(isset($call['file'])){
					if($file_matches){
						$file = substr($call['file'],strlen($file_matches));
						$file = "$file";
					}else{
						$file = $call['file'];
					}
				}else{
					$file = 'anon';
				}
				$line = @$call['line'];

				$args = '';
				foreach((array)@$call['args'] as $arg){
					ob_start();
					if(is_string($arg)) echo "'";
					print_r($arg);
					if(is_string($arg)) echo "'";
					$arg = preg_replace("/\s+/",' ',str_replace("\n",' ',ob_get_clean()));

					if(strlen($arg) > 50){
						$arg = substr($arg,0,47) . "...";
					}
					$args .="$arg, ";
				}
				$args = substr($args,0,-2);

				$object = @$call['class'] . @$call['type'];
				echo "#{$index} ".($file?$file:'root')."(".($line?$line:'?').") {$object}{$call['function']}({$args})\n";
			}
			$out = ob_get_clean();
		}catch(Exception $e){
			$out = '';
		}
		return $out;
	}

}
