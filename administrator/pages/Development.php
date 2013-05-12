<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

switch(Url::getParts('act')){
	case 'tasks':
		ImportFile(Url::getAdminDirBase() . DS . "pages/development/Tasks.php");
		Tasks();
		break;
	
	case 'ajaxHandler':
		ImportFile(Url::getAdminDirBase() . DS . "pages/development/AjaxHandler.php");
		Tasks();
		break;
	
	case 'configGenerator':
		ImportFile(Url::getAdminDirBase() . DS . "pages/development/ConfigGenerator.php");
		Tasks();
		break;
	
	default:
		// Unknown state
		echo "Unknown Error";
		
		// Redirect it to the home page
		Url::redirect(Url::home(), 2, false);
		break;
}
?>