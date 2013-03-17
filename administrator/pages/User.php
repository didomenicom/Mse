<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

switch(Url::getParts('act')){
	case 'login':
		ImportClass("User.UserActions");
		UserActions::login();
		break;
	
	case 'logout':
		ImportClass("User.UserActions");
		UserActions::logout();
		break;
	
	case 'resetPass':
		ImportClass("User.UserActions");
		UserActions::resetPassword();
		break;
	
	default:
		// Unknown state
		echo "Unknown Error";
		
		// Redirect it to the home page
		Url::redirect(Url::home(), 2, false);
		break;
}
?>