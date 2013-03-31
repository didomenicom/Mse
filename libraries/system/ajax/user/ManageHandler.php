<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

function Handler($task){
	if(isset($task) && $task != NULL){
		// Handles updating the list
		if($task === "updateUserList"){
			// Grab the walker class
			ImportClass("User.Users");
			
			$filter['deleted'] = false;
			$filter['permissionGroup'] = (isset($_POST["val"]) ? ($_POST["val"] > 0 ? $_POST["val"] : NULL) : NULL);
			$items = new Users($filter);
			$output = "";
			
			if($items->rowsExist()){
				while($items->hasNext()){
					$row = $items->getNext();
					$output .= "" . $row->getName(1) . "||" . 
							"" . $row->getUsername(1) . "||" . 
							"" . $row->getEmail(1) . "||" . 
							"" . $row->getPermissionGroup(1) . "||" . 
							"" . $row->getLastLogin(1) . "||" . 
							"" . $row->getId();
					
					if($items->hasNext() == true){
						$output .= "**|||**";
					}
				}
			} else {
				$output .= "**|||**";
			}
			
			// Return it
			echo $output;
		}
	}
	
	return true;
}

?>