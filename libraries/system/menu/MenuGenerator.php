<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

class MenuGenerator {
	/**
	 * This function generates and prints all of the menus for the frontend and backend. 
	 * It will find all of the root menus directly and then recursively calls for all of 
	 * the root menu. 
	 * It uses the twitter bootstrap layout and classes for displaying them.
	 */
	public static function generate($position, $backend = false){
		global $db;
		ImportClass("Group.General");
		
		// See if it is a specific position
		$dbPosition = $db->fetchAssoc("SELECT * FROM menu_position WHERE backend='" . ($backend == true ? 1 : 0) . "' AND position='" . $position . "'");
		
		if(isset($dbPosition->id) && $dbPosition->id > 0){
			// Position exists
			$menu = $db->fetchObject("SELECT * FROM menu WHERE parent='0' AND position='" . $dbPosition->id . "' ORDER BY ordering ASC");
			
			// Check if there are any menus for the position
			if($menu > 0){
				// There are menus -- display them
				if($dbPosition->inline == false){
					echo "<ul class=\"nav\">";
				}
				
				// Loop through all of the menus
				while($db->fetchObjectHasNext() == true){
					$rootMenu = $db->fetchObjectGetNext();
					
					// Check if the user has access to view the item
					if(General::hasMenuAccess($rootMenu->id) == true){
						// Has access -- display it
						self::buildDropDownMenu($rootMenu, 0, $dbPosition->inline);
					}
				}
				
				// Cleanup stack
				$db->fetchObjectDestroy();
				
				if($dbPosition->inline == false){
					echo "</ul>";
				}
			}
		} else {
			// Default position
			// TODO: Error - There should not be a default position for menus
		}
	}
	
	/**
	 * This function will build all of the sub menus from the root menu recursively
	 * TODO: Check if db->fetchObject will need a destroy when nothing is found
	 */
	private static function buildDropDownMenu($info, $subMenuParentCount, $inline = false){
		global $db; 
		
		if($info->id >= 0){
			// Get count of child items
			$cnt = $db->fetchObject("SELECT * FROM menu WHERE parent='" . $info->id . "' ORDER BY ordering ASC");
			
			$urlBase = (Define::get('baseSystem') == 1 ? Url::getAdminHttpBase() : Url::getHttpBase());
			
			// Check if the menu has children 
			if($cnt > 0){
				// More children to process
				echo ($inline == false ? "<li class=\"dropdown" . ($subMenuParentCount >= 1 ? "-submenu" : "") . "\">" :  "<li>");
				
				// Display the current menu
				if($info->internal == 1){
					?>
				<a href="<?php echo $urlBase . DS . ($info->url !== "" ? $info->url : Url::getCurrentHttp() . "#"); ?>" data-target="<?php echo $urlBase; ?>" class="dropdown-toggle" data-toggle="dropdown"><?php echo stripslashes($info->name); ?></a>
					<?php
				} else {
					$httpExists = (substr_count($info->url, "http") > 0 ? true : false);
					?>
				<li><a href="<?php echo ($info->url !== "" ? ($httpExists == false ? "http://" : "") . $info->url : $urlBase . DS . Url::getCurrentHttp() . "#"); ?>" data-target="<?php echo $urlBase; ?>" class="dropdown-toggle" data-toggle="dropdown"><?php echo stripslashes($info->name); ?></a></li>
					<?php
				}
				
				
				if($inline == false){
					echo "<ul class=\"dropdown-menu\">";
				}
				
				// Loop through all of the children 
				while($db->fetchObjectHasNext() == true){
					$row = $db->fetchObjectGetNext();
					
					// Check if the current user has access
					if(General::hasMenuAccess($row->id) == true){
						// Has access -- display it
						self::buildDropDownMenu($row, ($subMenuParentCount + 1), $inline);
					}
				}
				
				// Cleanup stack
				$db->fetchObjectDestroy();
				
				echo ($inline == false ? "</ul></li>" : "</li>");
			} else {
				// No children print the current menu and we are done
				if($info->internal == 1){
					?>
				<li><a href="<?php echo $urlBase . DS . ($info->url !== "" ? $info->url : Url::getCurrentHttp() . "#"); ?>" data-target="<?php echo $urlBase; ?>"><?php echo stripslashes($info->name); ?></a></li>
					<?php
				} else {
					$httpExists = (substr_count($info->url, "http") > 0 ? true : false);
					?>
				<li><a href="<?php echo ($info->url !== "" ? ($httpExists == false ? "http://" : "") . $info->url : $urlBase . DS . Url::getCurrentHttp() . "#"); ?>" data-target="<?php echo $urlBase; ?>"><?php echo stripslashes($info->name); ?></a></li>
					<?php
				}
			}
		}
	}
	
	/**
	 * Checks if any menu items exist for a given menu position
	 * Returns true if there are, false otherwise
	 */
	public static function itemsExist($inputName){
		global $db;
		
		if(isset($inputName) && $inputName !== ""){
			$dbPosition = $db->fetchAssoc("SELECT * FROM menu_position WHERE backend='" . Define::get('baseSystem') . "' AND position='" . $inputName . "'");
			
			if(isset($dbPosition->id)){
				$count = $db->fetchObject("SELECT * FROM menu WHERE parent='0' AND position='" . $dbPosition->id . "' ORDER BY ordering ASC");
				$db->fetchObjectDestroy();
				
				return ($count > 0 ? true : false);
			}
		}
		
		return false;
	}
}

?>
