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
		ImportFile(Url::getAdminDirBase() . DS . "pages/system/users/Manage.php");
		Manage();
		break;
		
	case 'add':
	case 'edit':
		ImportFile(Url::getAdminDirBase() . DS . "pages/system/users/Edit.php");
		Edit();
		break;
		
	case 'delete':
		ImportFile(Url::getAdminDirBase() . DS . "pages/system/users/Delete.php");
		Delete();
		break;
	
	case 'details':
		ImportFile(Url::getAdminDirBase() . DS . "pages/system/users/Details.php");
		Details();
		break;
	
	default:
		// Unknown state
		echo "Unknown Error";
		
		// Redirect it to the home page
		Url::redirect(Url::home(), 2, false);
		break;
}
?>
