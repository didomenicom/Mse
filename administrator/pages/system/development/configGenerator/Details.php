<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

/**
 * TODO: Move to a global class like help
 */
function Details(){
	if(UserFunctions::getLoggedIn() != NULL && true == true){ // TODO: Add permission check
		ImportClass("Config.ConfigOption");
		
		$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
		$id = (Url::getParts('id') > 0 ? Url::getParts('id') : 0);
		
		if($id > 0){
			// Create the class
			$data = new ConfigOption($id);
			
			echo Text::pageTitle("Configuration");
			
			echo $data->display();
		} else {
			Messages::setMessage("An unknown error has occured", Define::get("MessageLevelError"));
			Url::redirect(Url::getAdminHttpBase() . "/index.php?option=development&act=configGenerator&task=manage", 0, false);
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(UserFunctions::getLoginUrl(), 0, false);
	}
}

?>
