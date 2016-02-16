<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

// Setup the default date
date_default_timezone_set("UTC");

// Check if this is version 5.3
if (!function_exists('version_compare') || version_compare(phpversion(), '5', '<')){
	// Earlier than v5
} else {
	// v5
}

if(!defined("PHP_MAJOR_VERSION") || !defined("PHP_MINOR_VERSION") || PHP_MAJOR_VERSION < 5 || PHP_MINOR_VERSION < 3){ // TODO: Cleanup/fix/move
	define("E_DEPRECATED", "");
	define("E_USER_DEPRECATED", "");
}

// Grab the Logging & Error Reporting Class
if(!class_exists('Log')){
	require_once(BASEPATH.INCLUDES . '/system/Log.php');
	require_once(BASEPATH.INCLUDES . '/miscellaneous/MseException.php');
}

// Get the importing class
if(!class_exists('Import')){
	require_once(BASEPATH.INCLUDES . '/system/Import.php');
}

// Grab the Defines Class
ImportClass("system.define");

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

// Grab the Config Class
ImportClass("system.config");
$Config = new Config();

// Configure the Log print settings
Log::setDisplayErrorPage(($Config->getSystemVar('displaySystemError') != null && $Config->getSystemVar('displaySystemError') == "true" ? true : false));

// Grab the Date Class
ImportClass("system.date");

// Setup the timezone for the system
Date::setSystemTimezone();

// Grab the Encryption Class
ImportClass("system.encryption");

// Grab the DB Class
ImportClass("system.db");
$db = new Db();

// Grab the Users Class
ImportClass("system.UserFunctions");

// Grab the Messages Class
ImportClass("Messages.Messages");

// Grab the Render Class
ImportClass("system.render");

// Grab the Menu Class
ImportClass("Menu.MenuGenerator");

// Grab the "helper classes"
ImportClass("Library");
ImportClass("ClassesLibrary");
ImportClass("Config.ConfigOption");

// Grab the Invalid ID Exception Class
ImportClass("Miscellaneous.InvalidIdException");

// Check if there is a default class for the user
// This file is located in /libraries/user/Startup.php
if(file_exists(BASEPATH.LIBRARY.USER . "/Startup.php")){
	ImportFile(BASEPATH.LIBRARY.USER . "/Startup.php");
}

?>
