<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

ImportClass("Library");

class ConfigOption extends Library {
	protected $recordInfo = array('id' => 0);
	private $recordText = array('id' => "ID");
	private $recordGets = array('id' => "getId");
	
	public function ConfigOption($inputId = 0){
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
			$info = $db->fetchAssoc("SELECT * FROM config WHERE id=" . self::getId());
			
			$this->recordInfo['component'] = $info->component;
			$this->recordText['component'] = "Component";
			$this->recordGets['component'] = "getComponent";
			$this->recordInfo['type'] = $info->type;
			$this->recordText['type'] = "Type";
			$this->recordGets['type'] = "getType";
			$this->recordInfo['name'] = $info->name;
			$this->recordText['name'] = "Name";
			$this->recordGets['name'] = "getName";
			$this->recordInfo['value'] = $info->value;
			$this->recordText['value'] = "Value";
			$this->recordGets['value'] = "getValue";
		}
	}
	
	public function getComponent($text = 0){
		if($text == 1){
			return stripslashes($this->recordInfo['component']);
		} else {
			return $this->recordInfo['component'];
		}
	}
	
	public function setComponent($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['component'] = $inputValue;
			return 1;
		}
		
		return 0;
	}
	
	public function getType($text = 0){
		if($text == 1){
			ImportClass("Config.ConfigTypes");
			
			return stripslashes(ConfigTypes::getName($this->recordInfo['type']));
		} else {
			return $this->recordInfo['type'];
		}
	}
	
	public function setType($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['type'] = $inputValue;
			return 1;
		}
		
		return 0;
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
	
	public function getValue($text = 0){
		if($text == 1){
			return stripslashes(isset($this->recordInfo['value']) ? $this->recordInfo['value']: "");
		} else {
			return (isset($this->recordInfo['value']) ? $this->recordInfo['value'] : "");
		}
	}
	
	public function setValue($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['value'] = $inputValue;
			return 1;
		}
		
		return 0;
	}
	
	public function canDelete(){
		return true; // TODO: Finish
	}
	
	public function delete(){
		global $db;
		
		if(self::getId() > 0 && self::canDelete() == true){
			return $db->delete("DELETE FROM config WHERE id=" . self::getId());
		}
		
		return false;
	}
	
	public function save(){
		global $db;
		if($this->recordInfo['id'] > 0){
			$result = $db->update("UPDATE config SET " . 
				"component='" . addslashes(self::getComponent()) . "', " . 
				"type='" . addslashes(self::getType()) . "', " . 
				"name='" . addslashes(self::getName()) . "', " . 
				"value='" . addslashes(self::getValue()) . "' " . 
				"WHERE id=" . addslashes(self::getId()));
		
			if($result == true){
				$changes = "";
				self::determineClassChanges($this, $changes, $this->recordText);
				
				Log::action("Config (" . self::getId() . ") edited: " . ($changes != "" ? $changes : "None"));
			}
		} else {
			$result = $db->insert("INSERT INTO config (component, type, name, value) VALUES (" . 
				"'" . addslashes(self::getComponent()) . "', " . 
				"'" . addslashes(self::getType()) . "', " . 
				"'" . addslashes(self::getName()) . "', " . 
				"'" . addslashes(self::getValue()) . "')");
				
			if($result == true){
				Log::action("Config (" . self::getId() . ") added");
			}
		}
		
		return $result;
	}
	
	public function display(){
		self::displayInfo($this, $this->recordText, $this->recordGets);
	}
	
	private function getTypeText($inputId){
		switch($inputId){
			default:
				return "Unknown";
				break;
		}
	}
}
?>