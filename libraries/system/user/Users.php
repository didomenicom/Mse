<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

ImportClass("ClassesLibrary");

class Users extends ClassesLibrary {
	private $recordIndex = 0;
	private $recordQueryArray = array();
	private $filter;
	private $rowCount = 0;
	private $totalRows = 0;
	
	public function Users($filter = array()){
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
		
		// Filters
		$filterString = ClassesLibrary::generateFilterString($filter, $filterArray, $filterLogic);
		
		
		// Execute query
		$rowsCount = $db->fetchObject("SELECT id FROM users" . $filterString . ClassesLibrary::generateSortingString() . ClassesLibrary::generateRowsCountString());
		
		if($rowsCount > 0){
			ImportClass("User.User");
			
			while($db->fetchObjectHasNext() == true){
				$row = $db->fetchObjectGetNext();
				
				$this->recordQueryArray[$this->rowCount] = new User($row->id);
				$this->rowCount++;
				$this->totalRows++;
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