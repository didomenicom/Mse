<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
define("Access", 1);

session_start();

// Base File Path
$basePathParts = explode("/", dirname(__FILE__));
for($i = 0; $i < count($basePathParts); $i++)
	if($basePathParts[$i] === "administrator")
		unset($basePathParts[$i]);

define('BASEPATH', implode("/", $basePathParts));
unset($basePathParts);

// System
require_once(BASEPATH . '/includes/system/defines.php');
require_once(BASEPATH.INCLUDES . '/system/System.php');

// Mark this as administrator
Define::add('baseSystem', 1);

// Render
$Render = new Render();

// Find out if this is a ajax(jquery) request
if(isset($_GET["ajaxRequest"]) && $_GET["ajaxRequest"] == 1){ // TODO: Add more authentication based on $_SERVER variables (HTTP_X_REQUESTED_WITH)
	// Include Ajax classes
	ImportClass("Ajax.Ajax");
	
	// Create a new handler
	$ajax = new Ajax();
	
	// Call the handler
	$result = $ajax->handler();
	
	// Check if the request was handled properly 
	if($result == false){
		// An error has occurred, return failure
		echo -100;
	}
} else {
	// Handle template
	if($Render->loadTemplateFile($Config->getSystemVar('administratorTemplate'), Define::get('adminTemplate')) == true){
		// Template loaded successfully
		
		// Parse the template file
		if($Render->parseTemplateFile() == true){
			// Add settings
			$Render->templateSetting('jquery', true);
			
			// Render it
			echo $Render->renderPage();
		} else {
			Log::fatal("Index: parseTemplateFile -- Error");
		}
	} else {
		Log::fatal("Index: loadTemplateFile -- Error");
	}
}
?>
