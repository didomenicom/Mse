<?php
ImportClass("Library");
Log::setDisplayErrorPage(true);

class Task {
	protected $recordInfo = array('id' => 0);
	private $recordText = array('id' => "ID");
	private $recordGets = array('id' => "getId");
	
	public function Task($inputId = 0){
		if($inputId > 0){
			self::setId($inputId);
		}
	}
	
	public function setId($inputId, $buildData = 0){
		if($inputId > 0){
			$this->recordInfo['id'] = $inputId;
			
			if($buildData == 0){
				self::buildData();
			}
			
			return 1;
		}
		
		return 0;
	}
	
	public function getId(){
		return $this->recordInfo['id'];
	}
	
	private function buildData(){
		global $db;
		if(self::getId() > 0){
			// Search the db for the group
			$info = $db->fetchAssoc("SELECT * FROM developmentTasks WHERE id=" . self::getId());
			
			$this->recordInfo['name'] = $info->name;
			$this->recordText['name'] = "Name";
			$this->recordGets['name'] = "getName";
			$this->recordInfo['description'] = $info->description;
			$this->recordText['description'] = "Description";
			$this->recordGets['description'] = "getDescription";
			$this->recordInfo['completed'] = $info->completed;
			$this->recordText['completed'] = "Completed";
			$this->recordGets['completed'] = "getCompleted";
		}
	}
	
	public function getName($text = 0){
		if($text == 1){
			return stripslashes($this->recordInfo['name']);
		} else {
			return $this->recordInfo['name'];
		}
	}
	
	public function setName($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['name'] = $inputValue;
			return 1;
		}
		
		return 0;
	}
	
	public function getDescription($text = 0){
		if($text == 1){
			return stripslashes($this->recordInfo['description']);
		} else {
			return $this->recordInfo['description'];
		}
	}
	
	public function setDescription($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['description'] = $inputValue;
			return 1;
		}
		
		return 0;
	}
	
	public function getCompleted($text = 0){
		if($text == 1){
			return self::getYesNoText($this->recordInfo['completed']);
		} else {
			return $this->recordInfo['completed'];
		}
	}
	
	public function setCompleted($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['completed'] = $inputValue;
			return 1;
		}
		
		return 0;
	}
	
	public function canDelete(){
		global $db;
		
		$check = $db->fetchObject("SELECT * FROM developmentTasks WHERE parent=" . self::getId());
		$db->fetchObjectDestroy();
		
		return ($check > 0 ? false : true);
	}
	
	public function delete(){
		global $db;
		if(self::getId() > 0 && self::canDelete() == true){
			return $db->delete("DELETE FROM developmentTasks WHERE id=" . self::getId());
		}
		
		return false;
	}
	
	public function complete(){
		self::setCompleted(true);
		self::save();
		
		return true;
	}
	
	public function save(){
		global $db;
		if($this->recordInfo['id'] > 0){
			$result = $db->update("UPDATE developmentTasks SET " . 
				"name='" . addslashes(self::getName()) . "', " . 
				"description='" . addslashes(self::getDescription()) . "', " . 
				"completed='" . addslashes(self::getCompleted()) . "' " . 
				"WHERE id=" . addslashes(self::getId()));
		} else {
			$result = $db->insert("INSERT INTO developmentTasks (name, description, completed) VALUES (" . 
				"'" . addslashes(self::getName()) . "', " . 
				"'" . addslashes(self::getDescription()) . "', " . 
				"'" . addslashes(self::getCompleted()) . "')");
		}
		
		return $result;
	}
	
	public function display(){
		self::displayInfo($this, $this->recordText, $this->recordGets);
	}
}
?>