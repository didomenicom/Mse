<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

class Users extends ClassesLibrary {
	private $recordIndex = 0;
	private $recordQueryArray = array();
	private $filter;
	private $rowCount = 0;
	private $totalRows = 0;
	
	public function Users($filter = array(), $sort = array(), $countArray = array()){
		global $db;
		
		// Build array to generate filtering string
		$filterArray = array("deleted", "permissionGroup");
		$filterLogic = array();
		
		if(isset($filter['deleted'])){
			if($filter['deleted'] == false){
				$filter['deleted'] = "0000-00-00 00:00:00";
			} elseif($filter['deleted'] == true){
				$filter['deleted'] = "0000-00-00 00:00:00";
				$filterLogic['deleted'] = false;
			}
		}
		
		// Setup the sorting
		$sort['by'] = (!isset($sort['by']) ? "id" : $sort['by']);
		$sort['direction'] = (!isset($sort['direction']) ? "ASC" : $sort['direction']);
		
		// Filters
		$filterString = ClassesLibrary::generateFilterString($filter, $filterArray, $filterLogic);
		
		// Execute query
		$rowsCount = $db->fetchObject("SELECT id FROM users" . $filterString . ClassesLibrary::generateSortingString($sort) . ClassesLibrary::generateRowsCountString($countArray));
		
		if($rowsCount > 0){
			ImportClass("User.User");
			
			while($db->fetchObjectHasNext() == true){
				$row = $db->fetchObjectGetNext();
				
				$user = new User($row->id);
				
				if(isset($filter['hasAccess']) && $filter['hasAccess'] == true){
					// This will only select items that the current logged in user has access to
					if(UserFunctions::getLoggedIn()->hasAccess($user->getPermissionGroup()) == true){
						$this->recordQueryArray[$this->rowCount] = $user;
						$this->rowCount++;
						$this->totalRows++;
					}
				} else {
					$this->recordQueryArray[$this->rowCount] = $user;
					$this->rowCount++;
					$this->totalRows++;
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
	
	public function getRowCount(){
		return $this->rowCount;
	}
	
	public function getTotalRows(){
		return $this->totalRows;
	}
	
	public function getStartNumber(){
		return 1;
	}
}
?>
