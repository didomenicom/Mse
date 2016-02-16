<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

class MenuPosition extends Library {
	protected $recordInfo = array('id' => 0);
	private $recordText = array('id' => "ID");
	private $recordGets = array('id' => "getId");
	
	public function MenuPosition($inputId = 0){
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
			$info = $db->fetchAssoc("SELECT * FROM menu_position WHERE id=" . self::getId());
			
			$this->recordInfo['name'] = $info->name;
			$this->recordText['name'] = "Name";
			$this->recordGets['name'] = "getName";
			$this->recordInfo['position'] = $info->position;
			$this->recordText['position'] = "Position";
			$this->recordGets['position'] = "getPosition";
			$this->recordInfo['backend'] = $info->backend;
			$this->recordText['backend'] = "Backend";
			$this->recordGets['backend'] = "getBackend";
			$this->recordInfo['inline'] = $info->inline;
			$this->recordText['inline'] = "Inline";
			$this->recordGets['inline'] = "getInline";
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
	
	public function getPosition($text = 0){
		if($text == 1){
			return stripslashes($this->recordInfo['position']);
		} else {
			return $this->recordInfo['position'];
		}
	}
	
	public function setPosition($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['position'] = $inputValue;
			return 1;
		}
		
		return 0;
	}
	
	public function getBackend($text = 0){
		if($text == 1){
			return self::getYesNoText($this->recordInfo['backend']);
		} else {
			return $this->recordInfo['backend'];
		}
	}
	
	public function setBackend($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['backend'] = $inputValue;
			return 1;
		}
		
		return 0;
	}
	
	public function getInline($text = 0){
		if($text == 1){
			return self::getYesNoText($this->recordInfo['inline']);
		} else {
			return $this->recordInfo['inline'];
		}
	}
	
	public function setInline($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['inline'] = $inputValue;
			return 1;
		}
		
		return 0;
	}
	


	public function canDelete(){
		global $db;
		
		$check = $db->fetchObject("SELECT * FROM menu WHERE position=" . self::getId());
		$db->fetchObjectDestroy();
		
		return ($check > 0 ? false : true);
	}
	
	public function delete(){
		global $db;
		if(self::getId() > 0 && self::canDelete() == true){
			return $db->delete("DELETE FROM menu_position WHERE id=" . self::getId());
		}
		
		return false;
	}
	
	public function save(){
		global $db;
		if($this->recordInfo['id'] > 0){
			$result = $db->update("UPDATE menu_position SET " . 
				"name='" . addslashes(self::getName()) . "', " . 
				"position='" . addslashes(self::getPosition()) . "', " . 
				"backend='" . addslashes(self::getBackend()) . "', " . 
				"inline='" . addslashes(self::getInline()) . "' " . 
				"WHERE id=" . addslashes(self::getId()));
				
			if($result == true){
				$changes = "";
				self::determineClassChanges($this, $changes, $this->recordText);
				
				Log::action("Menu Position (" . self::getId() . ") edited: " . ($changes != "" ? $changes : "None"));
			}
		} else {
			$result = $db->insert("INSERT INTO menu_position (name, position, backend, inline) VALUES (" . 
				"'" . addslashes(self::getName()) . "', " . 
				"'" . addslashes(self::getPosition()) . "', " . 
				"'" . addslashes(self::getBackend()) . "', " . 
				"'" . addslashes(self::getInline()) . "')");
				
			if($result == true){
				Log::action("Menu Position (" . self::getId() . ") added");
			}
		}
		
		return $result;
	}
	
	public function display(){
		self::displayInfo($this, $this->recordText, $this->recordGets);
	}
}
?>
