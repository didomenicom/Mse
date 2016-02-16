<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

class General {
	/**
	 * Determines if the current logged in user has access to the inputted menu
	 * Returns true if they do, false otherwise
	 * TODO: Remove function
	 */
	public static function hasMenuAccess($menuId){
		global $db;
		ImportClass("Menu.Menu");
		ImportClass("Group.Group");
		
		if(isset($menuId) && $menuId > 0){
			$menu = new Menu($menuId);
			$user = UserFunctions::getLoggedIn();
			
			// Check if this is open to the public (permission group -1)
			if($menu->getPermissionGroup() == -1){
				return true;
			}
			
			if(UserFunctions::getLoggedIn() != NULL){
				// Check if the user and menu have the same permission level. If they do, then they have access
				$menuPermissionGroupParts = explode("|", $menu->getPermissionGroup());
				if(in_array($user->getPermissionGroup(), $menuPermissionGroupParts) == true){
					return true;
				}
				
				// Check if the menu permission group has a parent
				foreach($menuPermissionGroupParts as $menuPermissionGroup){
					if(General::menuCheckParentAccess($menuPermissionGroup, $user->getPermissionGroup()) == true){
						return true;
					}
				}
				
				// Check if the menu has any children
				$menus = $db->fetchObject("SELECT * FROM menu WHERE parent='" . $menu->getId() . "' ORDER BY ordering ASC");
				
				if($menus > 0){
					// Children exist
					while($db->fetchObjectHasNext() == true){
						$rootMenu = $db->fetchObjectGetNext();
						
					}
					$db->fetchObjectDestroy();
					
				}
			}
		}
		
		return false;
	}
	
	/**
	 * TODO: Remove function
	 */
	private static function menuCheckParentAccess($menuPermissionGroup, $userPermissionGroup){
		ImportClass("Group.Group");
		$menuPermissionGroup = new Group($menuPermissionGroup);
		
		if($menuPermissionGroup->getParent() != 0){
			// Parent exists
			// Check if the parent matches
			$parent = new Group($menuPermissionGroup->getParent());
			
			if($parent->getId() == $userPermissionGroup){
				return true;
			} elseif($parent->getParent() != 0){
				return General::menuCheckParentAccess($parent->getId(), $userPermissionGroup);
			} else {
				return false;
			}
		}
	}
}
?>
