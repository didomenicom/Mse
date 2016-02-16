<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

/**
 * TODO: Move to a global class like help
 */
function Details(){
	if(UserFunctions::getLoggedIn() != NULL && true == true){ // TODO: Add permission check
		ImportClass("AjaxHandler.AjaxHandler");
		
		$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
		$id = (Url::getParts('id') > 0 ? Url::getParts('id') : 0);
		
		if($id > 0){
			// Create the class
			$data = new AjaxHandler($id);
			
			echo Text::pageTitle("Ajax Handler");
			
			echo $data->display();
		} else {
			Messages::setMessage("An unknown error has occurred", Define::get("MessageLevelError"));
			Url::redirect(Url::getAdminHttpBase() . "/index.php?option=development&act=ajaxHandler&task=manage", 0, false);
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(Url::home(), 3, false);
	}
}

?>
