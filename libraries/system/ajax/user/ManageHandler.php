<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function Handler($task){
	if(isset($task) && $task != NULL){
		// Handles updating the list
		if($task === "updateUserList"){
			// Grab the walker class
			ImportClass("User.Users");
			
			$filter['hasAccess'] = true;
			$filter['deleted'] = (isset($_POST['deleted']) && $_POST['deleted'] == 1 ? true : false);
			$filter['permissionGroup'] = (isset($_POST["val"]) && $_POST["val"] > 0 ? $_POST["val"] : NULL);
			$items = new Users($filter);
			$output = "";
			
			if($items->rowsExist()){
				$cnt = 1;
				while($items->hasNext()){
					$row = $items->getNext();
					$output .= 
							"<tr>" . 
							"<td>" . 
								"<ul class=\"nav\" style=\"margin-top: 0px; margin-bottom: 0px;\">" . 
									"<li class=\"dropdown\">" . 
										"<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" style=\"margin-top: 0px; margin-bottom: 0px;\">" . $cnt . "</a>" . 
										"<ul class=\"dropdown-menu\">" . 
											"<li><a href=\"" . Url::getAdminHttpBase() . "/index.php?option=users&act=edit&id=" . $row->getId() . "\"><i class=\"icon-pencil\"></i> Edit</a></li>" . 
											"<li><a href=\"" . Url::getAdminHttpBase() . "/index.php?option=users&act=details&id=" . $row->getId() . "\"><i class=\"icon-list-alt\"></i> Details</a></li>" . 
											"<li><a href=\"" . Url::getAdminHttpBase() . "/index.php?option=users&act=delete&id=" . $row->getId() . "\"><i class=\"icon-trash\"></i> Delete</a></li>" . 
										"</ul>" . 
									"</li>" . 
								"</ul>" . 
							"</td>" . 
							"<td>" . $row->getName(1) . "</td>" . 
							"<td>" . $row->getUsername(1) . "</td>" . 
							"<td>" . $row->getEmail(1) . "</td>" . 
							"<td>" . $row->getPermissionGroup(1) . "</td>" . 
							"<td>" . $row->getLastLogin(1) . "</td>" . 
							"<td>" . $row->getId() . "</td>" . 
						"</tr>";
					$cnt++;
				}
			} else {
				$output = "0";
			}
			
			// Return it
			echo $output;
		}
		
		if($task === "updateSearch"){
			$output = "";
			
			if(isset($_POST["val"]) && $_POST["val"] !== ""){
				// Accept only characters (prevent sql injection)
				$matches = array();
				if(!preg_match("/[^A-Za-z0-9]/", $_POST['val'])){
					// TODO: Check if the string is not spaces 
					// Grab the class
					ImportClass("User.User");
					
					$resultsArray = User::search($_POST['val'], (isset($_POST['deleted']) && $_POST['deleted'] == 1 ? true : false));
					
					$cnt = 1;
					foreach($resultsArray as $resultId){
						$row = new User($resultId);
						
						$output .= 
								"<tr>" . 
								"<td>" . 
									"<ul class=\"nav\" style=\"margin-top: 0px; margin-bottom: 0px;\">" . 
										"<li class=\"dropdown\">" . 
											"<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" style=\"margin-top: 0px; margin-bottom: 0px;\">" . $cnt . "</a>" . 
											"<ul class=\"dropdown-menu\">" . 
												"<li><a href=\"" . Url::getAdminHttpBase() . "/index.php?option=users&act=edit&id=" . $row->getId() . "\"><i class=\"icon-pencil\"></i> Edit</a></li>" . 
												"<li><a href=\"" . Url::getAdminHttpBase() . "/index.php?option=users&act=details&id=" . $row->getId() . "\"><i class=\"icon-list-alt\"></i> Details</a></li>" . 
												"<li><a href=\"" . Url::getAdminHttpBase() . "/index.php?option=users&act=delete&id=" . $row->getId() . "\"><i class=\"icon-trash\"></i> Delete</a></li>" . 
											"</ul>" . 
										"</li>" . 
									"</ul>" . 
								"</td>" . 
								"<td>" . $row->getName(1) . "</td>" . 
								"<td>" . $row->getUsername(1) . "</td>" . 
								"<td>" . $row->getEmail(1) . "</td>" . 
								"<td>" . $row->getPermissionGroup(1) . "</td>" . 
								"<td>" . $row->getLastLogin(1) . "</td>" . 
								"<td>" . $row->getId() . "</td>" . 
							"</tr>";
						$cnt++;
					}
				} else {
					$output = "0";
				}
			} else {
				$output = "0";
			}
			
			// Return it
			echo $output;
		}
		
		if($task === "sort"){
			if(isset($_POST["val"]) && $_POST["val"] !== ""){
				$inputSort = $_POST["val"];
				
				// Remove the 'sort_' from the input
				if(substr($inputSort, 0, 5) === "sort_"){
					$inputSort = substr($inputSort, 5, (strlen($inputSort)));
				}
				
				$userSortBy = UserFunctions::getLoggedIn()->getSetting("sort.by.users"); 
				$userSortDirection = UserFunctions::getLoggedIn()->getSetting("sort.direction.users");
				
				if($inputSort === $userSortBy){
					// Update direction
					$userSortDirection = (strtoupper($userSortDirection) === "ASC" ? "DESC" : "ASC");
					
					// Save it
					UserFunctions::getLoggedIn()->setSettings("sort.direction.users", $userSortDirection);
					
					UserFunctions::getLoggedIn()->saveSettings();
					
					if(UserFunctions::getLoggedIn()->saveSettings() == true){
						echo 1;
					} else {
						echo 0;
					}
				} else {
					// Update by
					$userSortBy = $inputSort; // TODO: Check for valid sort
					
					// Update to default direction
					$userSortDirection = "ASC";
					
					// Save it
					UserFunctions::getLoggedIn()->setSettings("sort.by.users", $userSortBy);
					UserFunctions::getLoggedIn()->getSetting("sort.direction.users", $userSortDirection);
					
					if(UserFunctions::getLoggedIn()->saveSettings() == true){
						echo 1;
					} else {
						echo 0;
					}
				}
			}
		}
	}
	
	return true;
}

?>
