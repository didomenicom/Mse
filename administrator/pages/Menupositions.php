<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

switch(Url::getParts('act')){
	case 'manage':
		ImportFile(Url::getAdminDirBase() . DS . "pages/menus/positions/Manage.php");
		Manage();
		break;
		
	case 'add':
	case 'edit':
		ImportFile(Url::getAdminDirBase() . DS . "pages/menus/positions/Edit.php");
		Edit();
		break;
		
	case 'delete':
		ImportFile(Url::getAdminDirBase() . DS . "pages/menus/positions/Delete.php");
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