<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function Help(){
	switch(Url::getParts('task')){
		case 'manage':
			ImportFile(Url::getAdminDirBase() . DS . "pages/system/development/help/Manage.php");
			Manage();
			break;
			
		case 'add':
		case 'edit':
			ImportFile(Url::getAdminDirBase() . DS . "pages/system/development/help/Edit.php");
			Edit();
			break;
			
		case 'delete':
			ImportFile(Url::getAdminDirBase() . DS . "pages/system/development/help/Delete.php");
			Delete();
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
