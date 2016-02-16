<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function tasks(){
	switch(Url::getParts('task')){
		case 'manage':
			ImportFile(Url::getAdminDirBase() . DS . "pages/system/development/configGenerator/Manage.php");
			Manage();
			break;
			
		case 'add':
		case 'edit':
			ImportFile(Url::getAdminDirBase() . DS . "pages/system/development/configGenerator/Edit.php");
			Edit();
			break;
			
		case 'delete':
			ImportFile(Url::getAdminDirBase() . DS . "pages/system/development/configGenerator/Delete.php");
			Delete();
			break;
		
		case 'details':
			ImportFile(Url::getAdminDirBase() . DS . "pages/system/development/configGenerator/Details.php");
			Details();
			break;
		
		default:
			// Unknown state
			echo "Unknown Error";
			
			// Redirect it to the home page
			Url::redirect(Url::home(), 2, false);
			break;
	}
}

?>
