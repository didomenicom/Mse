<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

function tasks(){
	switch(Url::getParts('task')){
		case 'manage':
			ImportFile(Url::getAdminDirBase() . DS . "pages/system/development/tasks/Manage.php");
			Manage();
			break;
			
		case 'add':
		case 'edit':
			ImportFile(Url::getAdminDirBase() . DS . "pages/system/development/tasks/Edit.php");
			Edit();
			break;
			
		case 'delete':
			ImportFile(Url::getAdminDirBase() . DS . "pages/system/development/tasks/Delete.php");
			Delete();
			break;
		
		case 'complete':
			ImportFile(Url::getAdminDirBase() . DS . "pages/system/development/tasks/Complete.php");
			Complete();
			break;
		
		case 'verify':
			ImportFile(Url::getAdminDirBase() . DS . "pages/system/development/tasks/Verify.php");
			Verify();
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