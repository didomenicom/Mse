<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

class General {
	/**
	 * Determines if the current logged in user has access to the inputted menu
	 * Returns true if they do, false otherwise
	 */
	public static function hasMenuAccess($menuId){
		global $db;
		ImportClass("Menu.Menu");
		ImportClass("Group.Group");
		
		if(isset($menuId) && $menuId > 0){
			if(UserFunctions::getLoggedIn() != NULL){
				$menu = new Menu($menuId);
				$user = UserFunctions::getLoggedIn();
				
				// First off, check if the user and menu have the same permission level. If they do, then they have access
				if($menu->getPermissionGroup() == $user->getPermissionGroup()){
					return true;
				}
				
				// Check if the menu permission group has a parent
				if(General::menuCheckParentAccess($menu->getPermissionGroup(), $user->getPermissionGroup()) == true){
					return true;
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
	 * 
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