<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

function Complete(){
	if(UserFunctions::getLoggedIn() != NULL && true == true){
		ImportClass("Development.Tasks.Task");
		
		$id = (Url::getParts('id') > 0 ? Url::getParts('id') : 0);
		
		if($id > 0){
			// Create the class
			$data = new Task($id);
			
			if($data->getCompleted() == false){
				$info = Form::getParts();
				
				if($data->complete() == true){
					Messages::setMessage("Task Completed", Define::get("MessageLevelSuccess"));
				}
				
				Url::redirect(Url::getAdminHttpBase() . "/index.php?option=development&act=tasks&task=manage", 0, false);
			} else {
				Messages::setMessage("Task already completed", Define::get("MessageLevelError"));
				Url::redirect(Url::getAdminHttpBase() . "/index.php?option=development&act=tasks&task=manage", 0, false);
			}
		} else {
			Messages::setMessage("An unknown error has occured", Define::get("MessageLevelError"));
			Url::redirect(Url::getAdminHttpBase() . "/index.php?option=development&act=tasks&task=manage", 0, false);
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(UserFunctions::getLoginUrl(), 0, false);
	}
}

?>