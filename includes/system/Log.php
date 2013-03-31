<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

// Error level defines
define("LG_ALWAYS", 0);
define("LG_FATAL", 1);
define("LG_ERROR", 2);
define("LG_WARN", 3);
define("LG_INFO", 4);
define("LG_DEBUG", 5);
define("LG_ACTION", 6); // Log for user actions

require_once(BASEPATH.INCLUDES . '/log/Log.php');

/**
 * This function replaces the standard PHP error handler. 
 * If any errors happen, a message dump will be created and added to the
 * error array to be emailed out. 
 */
function phpErrorHandler($errorNumber, $errorString, $errorFile, $errorLine, $variables){
	$message = Log::createErrorMessage($errorNumber, $errorString, $errorFile, $errorLine, $variables);
	
	Log::always($message);
	Log::addErrorHandlerArray($message);
}

/**
 * This function replaces the standard PHP Exception Handler. 
 * If an exception is hit, report the error and stop execution. 
 * 
 * The exception is added to the error array and will be emailed out when 
 * execution finishes
 */
function phpExceptionHandler($e){
	Log::addErrorHandlerArray($e);
	die();
}

/**
 * This function will be called once all of the PHP execution has completed. 
 * It is going to check for errors and if there are any, it will combine all 
 * and email it to the email address.
 * Note: The email address is hard coded since all execution has completed.
 * TODO: See if email can be grabbed from Config
 */
function errorHandlerCleanupFunction(){
	// Grab all of the errors
	$messageArray = Log::getErrorHandlerArray();
	
	if(error_get_last() != NULL){
		$errorArray = error_get_last();
		
		$message = Log::createErrorMessage($errorArray['type'], $errorArray['message'], $errorArray['file'], $errorArray['line'], NULL);
		
		array_push($messageArray, $message);
	}
	
	if(is_array($messageArray) && count($messageArray) > 0){
		$message = "";
		
		foreach($messageArray as $msg){
			$message .= $msg;
		}
		
		// Send an email to the admin
		$result = @mail("systemMessage@mouseware.net", "System Message", $message);
		
		if($result == false){
			Log::error("Email Failed: " . $message);
			error_log("Email Error Handler Failed: " . $message, 0);
		}
		
		// Display error page
		if(Log::getDisplayErrorPage() == true){
			echo "A system error has occured";
		}
	}
}

// Setup the error handling
$old_error_handler = set_error_handler("phpErrorHandler");
$old_exception_handler = set_exception_handler("phpExceptionHandler");
register_shutdown_function('errorHandlerCleanupFunction');

?>