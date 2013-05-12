<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
define("Access", 1);

// Base File Path
$basePathParts = explode("/", dirname(__FILE__));
for($i = 0; $i < count($basePathParts); $i++)
	if($basePathParts[$i] === "installation")
		unset($basePathParts[$i]);

define('BASEPATH', implode("/", $basePathParts));
unset($basePathParts);

// System
require_once(BASEPATH . '/includes/system/defines.php');

// Setup the default date
date_default_timezone_set("UTC");

// Grab the Logging & Error Reporting Class
if(!class_exists('Log')){
	require_once(BASEPATH.INCLUDES . '/system/Log.php');
}

// Get the importing class
if(!class_exists('Import')){
	require_once(BASEPATH.INCLUDES . '/system/Import.php');
}

// Grab the File Class
ImportClass("files.file");

// Grab the Text Class
ImportClass("system.text");

// Grab the superglobals
ImportClass("system.superglobals");

// Grab the URL Class
ImportClass("system.url");

// Grab the Form Class
ImportClass("misc.form");

// Grab the Date Class
ImportClass("system.date");

// Setup the timezone for the system
Date::setSystemTimezone();

// Grab the Encryption Class
ImportClass("system.encryption");

// Grab the DB Class
ImportClass("system.db");
$db = new Db();

// Grab the Messages Class
ImportClass("Messages.Messages");

// Grab the Render Class
ImportClass("system.render");

// Grab the Menu Class
ImportClass("Menu.MenuGenerator");

// Render
$Render = new Render();

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
?>