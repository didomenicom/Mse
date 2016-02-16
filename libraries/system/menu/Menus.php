<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

// TODO: Display heigharcy 
class Menus extends ClassesLibrary {
	private $recordIndex = 0;
	private $recordQueryArray = array();
	private $filter;
	private $rowCount = 0;
	
	public function Menus($filter = array(), $sort = array()){
		global $db;
		
		// Build array to generate filtering string
		$filterArray = array("parent", "position", "permissionGroup");
		$filterLogic = array();
		
		// Setup the sorting
		$sort['by'] = (!isset($sort['by']) ? "id" : $sort['by']);
		$sort['direction'] = (!isset($sort['direction']) ? "ASC" : $sort['direction']);
		
		// Filters
		$filterString = ClassesLibrary::generateFilterString($filter, $filterArray, $filterLogic);
		
		// Execute query
		$rowsCount = $db->fetchObject("SELECT id FROM menu" . $filterString . ClassesLibrary::generateSortingString($sort) . ClassesLibrary::generateRowsCountString());
		
		if($rowsCount > 0){
			ImportClass("Menu.Menu");
			
			while($db->fetchObjectHasNext() == true){
				$row = $db->fetchObjectGetNext();
				
				$menu = new Menu($row->id);
				
				if(isset($filter['hasAccess']) && $filter['hasAccess'] == true){
					// This will only select items that the current logged in user has access to
					if(UserFunctions::getLoggedIn()->hasAccess($menu->getPermissionGroup()) == true){
						$this->recordQueryArray[$this->rowCount] = $menu;
						$this->rowCount++;
					}
				} else {
					$this->recordQueryArray[$this->rowCount] = $menu;
					$this->rowCount++;
				}
			}
			$db->fetchObjectDestroy();
		}
	}
	
	public function getFilter(){
		return $this->filter;
	}
	
	public function hasNext(){
		return ($this->recordIndex < $this->rowCount ? true : false);
	}
	
	public function getNext(){
		$result = $this->recordQueryArray[$this->recordIndex];
		$this->recordIndex++;
		return $result;
	}
	
	public function rowsExist(){
		return ($this->rowCount > 0);
	}
	
	private function getRowCount(){
		return $this->rowCount;
	}
}
?>
