<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

// Setup the default date
date_default_timezone_set("UTC");

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

?>