<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

/**
 * Default content function if nothing is set in the url. 
 * Required for the system to work. 
 */
$tmpVarA;
function home(){
	if(UserFunctions::getLoggedIn() == NULL){
		Messages::setMessage("Permission Denied. Please login", Define::get("MessageLevelError"));
		Url::redirect(UserFunctions::getLoginUrl(), 0, false);
	}
}
?>