<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

ImportClass("ClassesLibrary");

class ShortLinks extends ClassesLibrary {
	private $recordIndex = 0;
	private $recordQueryArray = array();
	private $filter;
	private $rowCount = 0;
	private $totalRows = 0;
	
	public function ShortLinks($filter = array()){
		global $db;
		
		// Execute query
		$rowsCount = $db->fetchObject("SELECT * FROM shortLink" . ClassesLibrary::generateSortingString() . ClassesLibrary::generateRowsCountString());
		
		if($rowsCount > 0){
			while($db->fetchObjectHasNext() == true){
				$row = $db->fetchObjectGetNext();
				
				$this->recordQueryArray[$this->rowCount] = $row;
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