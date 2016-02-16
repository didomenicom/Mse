<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

class Groups {
	private $recordIndex = 0;
	private $recordQueryArray = array();
	private $filter;
	private $rowCount = 0;
	
	public function Groups($filter = 0){
		global $db;
		
		$rowsCount = $db->fetchObject("SELECT id FROM permissionGroups");
		
		if($rowsCount > 0){
			ImportClass("Group.Group");
			
			while($db->fetchObjectHasNext() == true){
				$row = $db->fetchObjectGetNext();
				
				$group = new Group($row->id);
				
				if(isset($filter['hasAccess']) && $filter['hasAccess'] == true){
					// This will only select items that the current logged in user has access to
					if(UserFunctions::getLoggedIn()->hasAccess($row->id) == true){
						$this->recordQueryArray[$this->rowCount] = $group;
						$this->rowCount++;
					}
				} else {
					$this->recordQueryArray[$this->rowCount] = $group;
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
