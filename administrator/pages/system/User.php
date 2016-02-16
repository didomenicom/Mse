<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
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
	
	case 'changePass':
		ImportClass("User.UserActions");
		UserActions::changePassword();
		break;
	
	default:
		// Unknown state
		echo "Unknown Error";
		
		// Redirect it to the home page
		Url::redirect(Url::home(), 2, false);
		break;
}
?>
