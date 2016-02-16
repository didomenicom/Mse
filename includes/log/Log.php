<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

class Log {
	private static $logArray = array();
	private static $errorHandlerArray = array();
	private static $displayErrorPage = false;
	
	/**
	 * Takes a message and puts it on the array
	 * Message will be stored to database (if exists) 
	 * during the cleanup function
	 * TODO: Rename function to something that makes more sense
	 * TODO: Fix memory error with database insert
	 */
	private static function report($level, $message, $store = true){
		global $db;
		
		if(isset($level) && isset($message)){
			if($level >= 0 && $message !== ""){
				self::$logArray[count(self::$logArray)] = array("level" => $level, "message" => $message);
				
				// If the database is connected try to log it
				if(isset($db) && $db != NULL && $db->isConnected() == true){
					// Database is connected
					
					if(class_exists("Importer") == true && class_exists("UserFunctions") == true && $store == true){
						// Insert row
						$res = $db->insert("INSERT INTO syslog (uid, location, level, message, timestamp) VALUES (" . 
							"'" . (UserFunctions::getUserId() != NULL && is_integer(UserFunctions::getUserId()) ? UserFunctions::getUserId() : 0) . "', " . // TODO: There is a condition when the cookie exists and the session record doesn't exist where getUserId returns the User class object. To view this error, comment out the catch block in UserFunctions::getUserId()
							"'" . (substr_count($_SERVER['PHP_SELF'], "administrator") > 0 ? "Backend" : "Frontend") . "', " . 
							"'" . addslashes($level) . "', " . 
							"'" . addslashes($message) . "', " . 
							"'" . date("Y-m-d H:i:s", time()) . "')");
						
						if($res == 1){
							return true;
						}
					}
				}
				
				return true;
			} else {
				// Fatal error
				Log::fatal("Log: Report level ('" . $level . "') and message ('" . $message . "') error");
			}
		} else {
			// Fatal error
			Log::fatal("Log: Report level ('" . $level . "') or message ('" . $message . "') not set");
		}
		
		return false;
	}
	
	/**
	 * This function takes a stack trace and prints it out to text. 
	 * It pulls this function off the stack trace. 
	 * TODO: Remove any Log class calls
	 */
	public static function stackTrace($text = 0){
		$array = debug_backtrace();
		
		// The first one should be this function -- remove it
		array_pop($array);
		
		switch($text){
			case 0:
				if(Log::getDisplayErrorPage() == true){
					for($i = 0; $i < count($array); $i++){
						// Format: (Line Number) File Name
						if(isset($array[$i]['line']) && isset($array[$i]['file'])){
							echo "(" . $array[$i]['line'] . ") " . $array[$i]['file'] . "<br />";
						}
					}
					
					echo "<br />";
				}
				break;
			case 1:
				$str = "";
				for($i = 0; $i < count($array); $i++){
					if(isset($array[$i]['line']) && isset($array[$i]['file'])){
						$str .= "(" . $array[$i]['line'] . ") " . $array[$i]['file'] . "<br />";
					}
				}
				
				$str .= "<br />";
				
				return $str;
				break;
			case 2:
				$str = "";
				for($i = 0; $i < count($array); $i++){
					if(isset($array[$i]['line']) && isset($array[$i]['file'])){
						$str .= "(" . $array[$i]['line'] . ") " . $array[$i]['file'] . "\n";
					}
				}
				
				return $str;
				break;
			default:
				break;
		}
	}
	
	/**
	 * 
	 */
	public static function always($message){
		return self::report(LG_ALWAYS, $message);
	}
	
	/**
	 * This function is a fatal error. Fatal errors are logged, displayed on the screen, and an email is sent. 
	 * Fatal error should only be used if there is a problem in the code and we cannot continue. 
	 */
	public static function fatal($message){
		$success = true;
		
		// Log the error
		if(self::report(LG_FATAL, $message) == false){
			$success = false;
		}
		
		// Print the error to screen
		if(Log::getDisplayErrorPage() == true){
			echo $message;
		}
		
		// Add it to the list of errors which need to be emailed
		$message = $message . "\n" . self::stackTrace(2);
		if(Log::addErrorHandlerArray($message) == false){
			$success = false;
		}
		
		return $success;
	}
	
	/**
	 * This function is an error. Errors are logged and an email is sent. 
	 * Error should only be used if there is a non-fatal problem with the code. 
	 */
	public static function error($message){
		$success = true;
		
		// Log the error
		if(self::report(LG_ERROR, $message) == false){
			$success = false;
		}
		
		// Add it to the list of errors which need to be emailed
		$message = $message . "\n" . self::stackTrace(2);
		if(Log::addErrorHandlerArray($message) == false){
			$success = false;
		}
		
		return $success;
	}
	
	/**
	 * This function is a warning. Warnings are only logged. 
	 */
	public static function warn($message){
		return self::report(LG_WARN, $message);
	}
	
	/**
	 * This function is an info message. Info messages are only logged. 
	 */
	public static function info($message){
		return self::report(LG_INFO, $message);
	}
	
	/**
	 * This function is a debug message. Debug messages are only logged. 
	 */
	public static function debug($message){
		return self::report(LG_DEBUG, $message);
	}
	
	/**
	 * This function is an action message. Action messages are only logged. 
	 */
	public static function action($message){
		return self::report(LG_ACTION, $message);
	}
	
	/**
	 * This function is a call to deprecated code. Deprecated messages are logged and an email is sent. 
	 */
	public static function deprecated($message = ""){
		$success = true;
		$callers=debug_backtrace();
		$outputString = "";
		
		if(isset($callers[1])){
			$outputString .= "Deprecated function \"" . (isset($callers[1]['class']) ? $callers[1]['class'] . "." : "") . $callers[1]['function'] . "()\"";
			
			if(isset($callers[2])){
				$outputString .= " called from \"" . (isset($callers[1]['class']) ? $callers[1]['class'] . "." : "") . "." . $callers[2]['function'] . "()\"";
			}
			
			$outputString .= "\n";
		}
		
		if(strlen($message) > 0){
			$outputString .= (strlen($outputString) > 0 ? "\t" : "") . $message . "\n";
		}
		
		if(strlen($outputString) > 0){
			$outputString .= "\t";
			$outputString .= "Call Stack: \n";
			$outputString .= self::stackTrace(2) . "\n\n";
		}
		
		if(strlen($outputString) > 0){
			$outputString .= "\t";
			$outputString .= "Redirected from: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "N/A") . "\n\n";
		}
		
		// Log the error
		if(self::report(LG_DEPRECATED, $outputString) == false){
			$success = false;
		}
		
		// Add it to the list of errors which need to be emailed
		if(Log::addErrorHandlerArray($outputString) == false){
			$success = false;
		}
		
		return $success;
	}
	
	/**
	 * 
	 */
	public static function getUserData(){
		
	}
	
	/**
	 * 
	 */
	public static function addErrorHandlerArray($text){
		if(isset($text) && is_string($text)){
			array_push(self::$errorHandlerArray, $text);
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * 
	 */
	public static function getErrorHandlerArray(){
		return self::$errorHandlerArray;
	}
	
	/**
	 * This function will pop off and return a single error from the array if there are 
	 * items in the array. 
	 * If there are no items in the array, null is returned. 
	 */
	public static function removeErrorHandlerFromArray(){
		if(is_array(self::$errorHandlerArray) && count(self::$errorHandlerArray) > 0){
			return array_pop(self::$errorHandlerArray);
		}
		
		return null;
	}
	
	/**
	 * This function will return the size of the array. If there is no array, 0 is returned. 
	 */
	public static function getErrorHandlerArrayCount(){
		if(is_array(self::$errorHandlerArray)){
			return count(self::$errorHandlerArray);
		}
		
		return 0;
	}
	
	/**
	 * 
	 */
	public static function getDisplayErrorPage(){
		return self::$displayErrorPage;
	}
	
	/**
	 * 
	 */
	public static function setDisplayErrorPage($val){
		self::$displayErrorPage = $val;
	}
	
	/**
	 * This function creates an error message dump of the system. 
	 * Note: The stack trace will include the call into this function
	 * TODO: Remove Log in the stack trace (Create condensed stack trace)
	 */
	public static function createErrorMessage($errorNumber, $errorString, $errorFile, $errorLine, $variables){
		$message = "";
		$timestamp = date("Y-m-d H:i:s (T)", time());
		
		$errorTypes = array(
			E_ERROR 				=> 'Error',
			E_WARNING 				=> 'Warning',
			E_PARSE 				=> 'Parsing Error',
			E_NOTICE 				=> 'Notice',
			E_CORE_ERROR 			=> 'Core Error',
			E_CORE_WARNING 			=> 'Core Warning',
			E_COMPILE_ERROR 		=> 'Compile Error',
			E_COMPILE_WARNING 		=> 'Compile Warning',
			E_USER_ERROR 			=> 'User Error',
			E_USER_WARNING 			=> 'User Warning',
			E_USER_NOTICE 			=> 'User Notice',
			E_STRICT 				=> 'Runtime Notice',
			E_RECOVERABLE_ERROR 	=> 'Catchable Fatal Error',
			E_DEPRECATED 			=> 'Deprecated',
			E_USER_DEPRECATED 		=> 'User Deprecated'
			);
		
		$message = "<error>\n";
		$message .= "\t<timestamp>" . $timestamp . "</timestamp>\n";
		$message .= "\t<number>" . $errorNumber . "</number>\n";
		$message .= "\t<type>" . $errorTypes[$errorNumber] . "</type>\n";
		$message .= "\t<message>" . $errorString . "</message>\n";
		$message .= "\t<filename>" . $errorFile . "</filename>\n";
		$message .= "\t<line>" . $errorLine . "</line>\n";
		
		if(isset($variables)){
			// TODO: Handle recursive array
			$message .= "\t<variables>" . serialize($variables) . "</variables>\n";
		}
		
		$message .= "\t<trace>" . Log::stackTrace(2) . "</trace>\n";
		
		switch($errorNumber){
			// Fatal 
			case E_ERROR:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
			case E_RECOVERABLE_ERROR:
			case E_PARSE:
				Log::setDisplayErrorPage(true);
				$message .= "\t<userdata>" . Log::getUserData() . "</userdata>\n";
				break;
				
			// Warning
			case E_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			case E_USER_WARNING:
				Log::setDisplayErrorPage(true);
				break;
				
			// Notice
			case E_NOTICE:
			case E_USER_NOTICE:
			case E_STRICT:
			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				break;
				
			// Should not have
			case E_ALL:
			default:
				break;
		}
		
		$message .= "</error>\n\n";
		
		return $message;
	}
	
	// http://stackoverflow.com/questions/9105816/is-there-a-way-to-detect-circular-arrays-in-pure-php
	/**
	 * 
	 */
	private static function isRecursiveArray(array $array){
		$some_reference = new stdclass();
		return Log::isRecursiveArrayIteration($array, $some_reference);
	}
	
	/**
	 * 
	 */
	private static function isRecursiveArrayIteration(array & $array, $reference){
		$last_element = end($array);
		if($reference === $last_element){
			return true;
		}
		$array[] = $reference;
		
		foreach($array as &$element){
			if(is_array($element)){
				if(Log::isRecursiveArrayIteration($element, $reference)){
					Log::removeLastElementIfSame($array, $reference);
					return true;
				}
			}
		}
		
		Log::removeLastElementIfSame($array, $reference);
		
		return false;
	}
	
	private static function removeLastElementIfSame(array & $array, $reference){
		if(end($array) === $reference){
			unset($array[key($array)]);
		}
	}
	
	// http://stackoverflow.com/questions/804045/preferred-method-to-store-php-arrays-json-encode-vs-serialize
	/**
	 * 
	 */
	private static function iterate_array(&$arr){
		if(!is_array($arr)){
			print $arr;
			return;
		}
		
		// if this key is present, it means you already walked this array
		if(isset($arr['__been_here'])){
			print 'RECURSION';
			return;
		}
		
		$arr['__been_here'] = true;
		
		foreach($arr as $key => &$value){
			// print your values here, or do your stuff
			if($key !== '__been_here'){
				if(is_array($value)){
					Log::iterate_array($value);
				}
//				print $value;
			}
		}
		
		// you need to unset it when done because you're working with a reference...
		unset($arr['__been_here']);
	}
	
	// http://php.net/manual/en/function.microtime.php
	/**
	 * 
	 */
	public static function microtime_float(){
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}

?>
