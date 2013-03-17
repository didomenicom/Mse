<?php
class Tasks {
	private $recordIndex = 0;
	private $recordQueryArray = array();
	private $filter;
	private $rowCount = 0;
	
	public function Tasks($filter = 0){
		global $db;
		
		
		$rowsCount = $db->fetchObject("SELECT id FROM developmentTasks" . (isset($filter['completed']) ? ($filter['completed'] == true ? " WHERE completed=1" : " WHERE completed=0") : ""));
		
		if($rowsCount > 0){
			ImportClass("Development.Tasks.Task");
			
			while($db->fetchObjectHasNext() == true){
				$row = $db->fetchObjectGetNext();
				
				$this->recordQueryArray[$this->rowCount] = new Task($row->id);
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