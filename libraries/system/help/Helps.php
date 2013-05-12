<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

ImportClass("ClassesLibrary");

class Helps extends ClassesLibrary {
	private $recordIndex = 0;
	private $recordQueryArray = array();
	private $filter;
	private $rowCount = 0;
	
	public function Helps($filter = 0){
		global $db;
		
		// Build array to generate filtering string
		$filterArray = array("component", "id");
		$filterLogic = array();
		
		// Filters
		$filterString = ClassesLibrary::generateFilterString($filter, $filterArray, $filterLogic);
		
		// Execute query
		$rowsCount = $db->fetchObject("SELECT id FROM help" . $filterString . ClassesLibrary::generateSortingString() . ClassesLibrary::generateRowsCountString());
		
		if($rowsCount > 0){
			ImportClass("Help.Help");
			
			while($db->fetchObjectHasNext() == true){
				$row = $db->fetchObjectGetNext();
				
				$this->recordQueryArray[$this->rowCount] = new Help($row->id);
				$this->rowCount++;
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