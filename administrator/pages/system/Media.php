<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

switch(Url::getParts('act')){
	case 'manage':
		ImportFile(Url::getAdminDirBase() . DS . "pages/system/media/Manage.php");
		Manage();
		break;
	
	case 'add':
		ImportFile(Url::getAdminDirBase() . DS . "pages/system/media/Add.php");
		Add();
		break;
	
	default:
		// Unknown state
		echo "Unknown Error";
		
		// Redirect it to the home page
		Url::redirect(Url::home(), 2, false);
		break;
}
?>