<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2012 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

class Task extends Library {
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
			
			return true;
		}
		
		return false;
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
			
			$this->recordInfo['verifyBy'] = $info->verifyBy;
			$this->recordText['verifyBy'] = "Verify By";
			$this->recordGets['verifyBy'] = "getVerifyBy";
			
			$this->recordInfo['verified'] = $info->verified;
			$this->recordText['verified'] = "Verified";
			$this->recordGets['verified'] = "getVerified";
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
			return true;
		}
		
		return false;
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
			return true;
		}
		
		return false;
	}
	
	public function getCompleted($text = 0){
		if($text == 1){
			return self::getYesNoText($this->recordInfo['completed']);
		} else {
			return (isset($this->recordInfo['completed']) ? $this->recordInfo['completed'] : 0);
		}
	}
	
	public function setCompleted($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['completed'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	public function getVerifyBy($text = 0){
		if($text == 1){
			return stripslashes($this->recordInfo['verifyBy']);
		} else {
			return $this->recordInfo['verifyBy'];
		}
	}
	
	public function setVerifyBy($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['verifyBy'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	public function getVerified($text = 0){
		if($text == 1){
			return self::getYesNoText($this->recordInfo['verified']);
		} else {
			return (isset($this->recordInfo['verified']) ? $this->recordInfo['verified'] : 0);
		}
	}
	
	public function setVerified($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['verified'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	public function canDelete(){
		return true;
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
	
	public function verify(){
		self::setVerified(1);
		self::save();
		
		return true;
	}
	
	public function save(){
		global $db;
		if($this->recordInfo['id'] > 0){
			$result = $db->update("UPDATE developmentTasks SET " . 
				"name='" . addslashes(self::getName()) . "', " . 
				"description='" . addslashes(self::getDescription()) . "', " . 
				"completed='" . addslashes(self::getCompleted()) . "', " . 
				"verifyBy='" . addslashes(self::getVerifyBy()) . "', " . 
				"verified='" . addslashes(self::getVerified()) . "' " . 
				"WHERE id=" . addslashes(self::getId()));
		} else {
			$result = $db->insert("INSERT INTO developmentTasks (name, description, completed, verifyBy, verified) VALUES (" . 
				"'" . addslashes(self::getName()) . "', " . 
				"'" . addslashes(self::getDescription()) . "', " . 
				"'" . addslashes(self::getCompleted()) . "', " . 
				"'" . addslashes(self::getVerifyBy()) . "', " . 
				"'" . addslashes(self::getVerified()) . "')");
		}
		
		return $result;
	}
	
	public function display(){
		self::displayInfo($this, $this->recordText, $this->recordGets);
	}
}
?>