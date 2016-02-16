<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

switch(Url::getParts('act')){
	case 'manage':
		ImportFile(Url::getAdminDirBase() . DS . "pages/system/menus/Manage.php");
		Manage();
		break;
		
	case 'add':
	case 'edit':
		ImportFile(Url::getAdminDirBase() . DS . "pages/system/menus/Edit.php");
		Edit();
		break;
		
	case 'delete':
		ImportFile(Url::getAdminDirBase() . DS . "pages/system/menus/Delete.php");
		Delete();
		break;
	
	default:
		// Unknown state
		echo "Unknown Error";
		
		// Redirect it to the home page
		Url::redirect(Url::home(), 2, false);
		break;
}
?>
