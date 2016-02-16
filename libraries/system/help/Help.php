<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2013 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

// TODO: Rename to HelpMenu
class Help extends Library {
	protected $recordInfo = array('id' => 0);
	private $recordText = array('id' => "ID");
	private $recordGets = array('id' => "getId");
	
	public function Help($inputId = 0){
		if($inputId > 0){
			self::setId($inputId);
		}
	}
	
	public function setId($inputId, $buildData = true){
		if(is_numeric($inputId) && intval($inputId) > 0){
			$this->recordInfo['id'] = intval($inputId);
			
			if($buildData == true){
				return self::buildData();
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
			$info = $db->fetchAssoc("SELECT * FROM help WHERE id=" . self::getId());
			
			$this->recordInfo['component'] = $info->component;
			$this->recordText['component'] = "Component";
			$this->recordGets['component'] = "getComponent";
			$this->recordInfo['title'] = $info->title;
			$this->recordText['title'] = "Title";
			$this->recordGets['title'] = "getTitle";
			$this->recordInfo['content'] = $info->content;
			$this->recordText['content'] = "Content";
			$this->recordGets['content'] = "getContent";
		}
	}
	
	public function getComponent($text = 0){
		if($text == 1){
			if($this->recordInfo['component'] !== ""){
				ImportClass("Component.Component");
				
				$component = new Component($this->recordInfo['component']);
				return $component->getDisplayName();
			}
		} else {
			return $this->recordInfo['component'];
		}
	}
	
	public function setComponent($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['component'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	public function getTitle($text = 0){
		if($text == 1){
			return stripslashes($this->recordInfo['title']);
		} else {
			return $this->recordInfo['title'];
		}
	}
	
	public function setTitle($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['title'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	public function getContent($text = 0){
		if($text == 1){
			return stripslashes($this->recordInfo['content']);
		} else {
			return $this->recordInfo['content'];
		}
	}
	
	public function setContent($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['content'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	public function canDelete(){
		return true; // TODO: Finish
	}
	
	public function delete(){
		global $db;
		
		if(self::getId() > 0 && self::canDelete() == true){
			return $db->delete("DELETE FROM help WHERE id=" . self::getId());
		}
		
		return false;
	}
	
	// TODO: Redefine return to true or false. Optionally enter pointer to store number of records modified. 
	public function save(){
		global $db;
		if($this->recordInfo['id'] > 0){
			$result = $db->update("UPDATE help SET " . 
				"component='" . addslashes(self::getComponent()) . "', " . 
				"title='" . addslashes(self::getTitle()) . "', " . 
				"content='" . htmlspecialchars(addslashes(self::getContent())) . "' " . 
				"WHERE id=" . addslashes(self::getId()));
			
			if($result == 1){
				$changes = "";
				self::determineClassChanges($this, $changes, $this->recordText);
				
				Log::action("Help (" . self::getId() . ") edited: " . ($changes != "" ? $changes : "None"));
			}
		} else {
			$result = $db->insert("INSERT INTO help (component, title, content) VALUES (" . 
				"'" . addslashes(self::getComponent()) . "', " . 
				"'" . addslashes(self::getTitle()) . "', " . 
				"'" . htmlspecialchars(addslashes(self::getContent())) . "')");
			
			if($result == 1){
				self::setId($db->getLastInsertId());
				Log::action("Help (" . self::getId() . ") added");
			}
		}
		
		return $result;
	}
	
	public function display(){
		self::displayInfo($this, $this->recordText, $this->recordGets);
	}
}
?>
