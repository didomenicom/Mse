<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

function tasks(){
	switch(Url::getParts('task')){
		case 'manage':
			ImportFile(Url::getAdminDirBase() . DS . "pages/development/ajaxHandler/Manage.php");
			Manage();
			break;
			
		case 'add':
		case 'edit':
			ImportFile(Url::getAdminDirBase() . DS . "pages/development/ajaxHandler/Edit.php");
			Edit();
			break;
			
		case 'delete':
			ImportFile(Url::getAdminDirBase() . DS . "pages/development/ajaxHandler/Delete.php");
			Delete();
			break;
		
		case 'details':
			ImportFile(Url::getAdminDirBase() . DS . "pages/development/ajaxHandler/Details.php");
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