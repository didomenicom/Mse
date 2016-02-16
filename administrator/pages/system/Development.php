<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

switch(Url::getParts('act')){
	case 'ajaxHandler':
		ImportFile(Url::getAdminDirBase() . DS . "pages/system/development/AjaxHandler.php");
		Tasks();
		break;
	
	case 'apiHandler':
		ImportFile(Url::getAdminDirBase() . DS . "pages/system/development/ApiHandler.php");
		Tasks();
		break;
	
	case 'configGenerator':
		ImportFile(Url::getAdminDirBase() . DS . "pages/system/development/ConfigGenerator.php");
		Tasks();
		break;
	
	case 'help':
		ImportFile(Url::getAdminDirBase() . DS . "pages/system/development/Help.php");
		Help();
		break;
	
	default:
		// Unknown state
		echo "Unknown Error";
		
		// Redirect it to the home page
		Url::redirect(Url::home(), 2, false);
		break;
}
?>
