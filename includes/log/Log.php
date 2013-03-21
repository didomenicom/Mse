<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
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
	private static function report($level, $message){
		global $db;
		
		if(isset($level) && isset($message)){
			if($level >= 0 && $message !== ""){
				self::$logArray[count(self::$logArray)] = array("level" => $level, "message" => $message);
				
				// If the database is connected try to log it
//				if(isset($db) && $db != NULL && $db->isConnected() == true){
//					// Database is connected
//					if(class_exists("UserFunctions") == true && array_search("getLoggedIn", get_class_methods("UserFunctions")) !== NULL){
//						// Insert row
//						$res = $db->insert("INSERT INTO syslog (uid, location, level, message, timestamp) VALUES (" . 
//							"'" . (class_exists("ImportClass") == true && class_exists("UserFunctions") == true && UserFunctions::getLoggedIn() == true ? UserFunctions::getLoggedIn()->getUsername() : "") . "', " . 
//							"'" . (substr_count($_SERVER['PHP_SELF'], "administrator") > 0 ? "Backend" : "Frontend") . "', " . 
//							"'" . addslashes($level) . "', " . 
//							"'" . addslashes($message) . "', " . 
//							"'" . date("Y-m-d H:i:s", time()) . "')");
//						
//						if($res == 1){
//							return true;
//						}
//					}
//				}
				
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
				for($i = 0; $i < count($array); $i++){
					// Format: (Line Number) File Name
					if(isset($array[$i]['line']) && isset($array[$i]['file'])){
						echo "(" . $array[$i]['line'] . ") " . $array[$i]['file'] . "<br />";
					}
				}
				echo "<br />";
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
	
	public static function always($message){
		return self::report(LG_ALWAYS, $message);
	}
	
	public static function fatal($message){
		echo $message . "<br />";
		self::stackTrace();
		// TODO: Email
		return self::report(LG_FATAL, $message);
	}
	
	public static function error($message){
		return self::report(LG_ERROR, $message);
	}
	
	public static function warn($message){
		return self::report(LG_WARN, $message);
	}
	
	public static function info($message){
		return self::report(LG_INFO, $message);
	}
	
	public static function debug($message){
		return self::report(LG_DEBUG, $message);
	}
	
	public static function action($message){
		return self::report(LG_ACTION, $message);
	}
	
	public static function getUserData(){
		
	}
	
	public static function addErrorHandlerArray($text){
		if(isset($text)){
			array_push(self::$errorHandlerArray, $text);
		}
	}
	
	public static function getErrorHandlerArray(){
		return self::$errorHandlerArray;
	}
	
	public static function getDisplayErrorPage(){
		return self::$displayErrorPage;
	}
	
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
			$message .= "\t<variables>" . wddx_serialize_value($variables, "Variables") . "</variables>\n";
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
}

?>