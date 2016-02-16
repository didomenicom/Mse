<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
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
define("LG_DEPRECATED", 7); // Log for deprecated code

// Define who is receiving the email
if(!defined("LOG_EMAIL_RECIPIENT")){
	define("LOG_EMAIL_RECIPIENT", "systemmessage@mouseware.net");
}

require_once(BASEPATH.INCLUDES . '/log/Log.php');

/**
 * This function replaces the standard PHP error handler. 
 * If any errors happen, a message dump will be created and added to the
 * error array to be emailed out. 
 */
function phpErrorHandler($errorNumber, $errorString, $errorFile, $errorLine, $variables){
	// As per http://www.php.net//manual/en/language.operators.errorcontrol.php 
	// Check to make sure the error was not suppressed before logging it. 
	if(error_reporting() === 0){
		return;
	}
	
	$message = Log::createErrorMessage($errorNumber, $errorString, $errorFile, $errorLine, $variables);
	
	Log::always($message);
	Log::addErrorHandlerArray($message);
}

/**
 * This function replaces the standard PHP Exception Handler. 
 * If an exception is hit, report the error and stop execution. 
 * 
 * TODO: convert stack trace to string
 */
function phpExceptionHandler($e){
	Log::addErrorHandlerArray($e);
	ob_start();
	var_dump($someVar);
	$result = ob_get_clean();
	
	$message = "Uncaught exception - " . get_class($e) . ": \n" . 
		"Code: " . $e->getCode() . "\n" . 
		"Message: " . htmlentities($e->getMessage()) . "\n" . 
		"Stack Trace: " . $e->getTraceAsString() . "\n";
	
	// Send an email to the admin
	$result = @mail(LOG_EMAIL_RECIPIENT, "System Message", $message);
	
	if($result == false){
		Log::error("Email Exception Failed: " . $message);
		error_log("Email Exception Handler Failed: " . $message, 0);
	}
	
	// Display error page
	if(Log::getDisplayErrorPage() == true){
		echo "A system exception has occurred";
	}
}

/**
 * This function will be called once all of the PHP execution has completed. 
 * It is going to check for errors and if there are any, it will combine all 
 * and email it to the email address.
 * Note: The email address is hard coded since all execution has completed.
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
		$result = @mail(LOG_EMAIL_RECIPIENT, "System Message", $message);
		
		if($result == false){
			Log::error("Email Failed: " . $message);
			error_log("Email Error Handler Failed: " . $message, 0);
		}
		
		// Display error page
		if(Log::getDisplayErrorPage() == true){
			echo "A system error has occurred";
		}
	}
}

// Setup the error handling
$old_error_handler = set_error_handler("phpErrorHandler");
$old_exception_handler = set_exception_handler("phpExceptionHandler");
register_shutdown_function('errorHandlerCleanupFunction');

?>
