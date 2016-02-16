<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2013 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

/**
 * TODO: Add multi dimensional options for config (Ex: User options -> type). Only available if parent type is array
 */
class Component extends Library {
	protected $recordInfo = array('name' => "", 'id' => "");
	private $recordText = array('name' => "Name", 'id' => "Name");
	private $recordGets = array('name' => "getId", 'id' => "getId");
	private $update = false;
	
	public function Component($inputId = ""){
		self::setId($inputId);
	}
	
	// TODO: Test name as primary key/index
	public function setId($inputId, $buildData = true){ // TODO: Check to make sure all build data is true
		if(isset($inputId) && strlen($inputId) > 0){
			// Check if the key is valid (all lower case characters and '-')
			if(preg_match("/([a-z\-]+)/", $inputId)){
				$this->recordInfo['name'] = $inputId;
				
				if($buildData == true){ // TODO: Check to make sure all build data is true
					self::buildData();
					$this->update = true;
				}
					
				return true; // TODO: Check to make sure all of the other libraries return true (not 1)
			}
		}
		
		return false; // TODO: Check to make sure all of the other libraries return false (not 0)
	}
	
	public function getId(){
		return $this->recordInfo['name'];
	}
	
	private function buildData(){
		global $db;
		
		if(self::getId() !== ""){
			// TODO: Implement/Delete adminUrl
			$info = $db->fetchAssoc("SELECT * FROM components WHERE name='" . self::getId() . "'");
			
			$this->recordInfo['name'] = $info->name;
			$this->recordText['name'] = "Name";
			$this->recordGets['name'] = "getName";
			
			$this->recordInfo['displayName'] = $info->displayName;
			$this->recordText['displayName'] = "Display Name";
			$this->recordGets['displayName'] = "getDisplayName";
			
			$this->recordInfo['functions'] = $info->functions;
			$this->recordText['functions'] = "Functions";
			$this->recordGets['functions'] = "getFunctions";
			
			$this->recordInfo['defaultUrl'] = $info->defaultUrl;
			$this->recordText['defaultUrl'] = "Default URL";
			$this->recordGets['defaultUrl'] = "getDefaultUrl";
			
			$this->recordInfo['systemFunction'] = $info->systemFunction; // TODO: Rename to locked (not visible to webpage)
			$this->recordText['systemFunction'] = "System Function";
			$this->recordGets['systemFunction'] = "getSystemFunction";
			
			$this->recordInfo['active'] = $info->active;
			$this->recordText['active'] = "Active";
			$this->recordGets['active'] = "getActive";
		}
	}
	
	public function getName(){
		return self::getId();
	}
	
	public function getDisplayName(){
		return stripslashes($this->recordInfo['displayName']);
	}
	
	public function setDisplayName($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['displayName'] = $inputValue;
			
			if(strlen($this->recordInfo['name']) == 0){
				// Generate an index
				$nameIndex = preg_replace("/\s/", "-", strtolower($this->recordInfo['displayName']));
				
				// Check if the index exists
				if(self::exists($nameIndex) == true){
					$nameIndex = $nameIndex . "--1";
					$indx = 1;
					// Loop adding a number on the end if there is more than 1 of the same name
					while(self::exists($nameIndex) == true){
						$nameIndex = substr($nameIndex, 0, (strlen($nameIndex) - strlen($indx))) . ($indx++);
					}
				}
				
				// This should be a unique value... store it
				$this->recordInfo['name'] = $nameIndex;
			}
			
			return true;
		}
		
		return false;
	}
	
	public function getFunctions($text = false){
		if($text == true){
			$parts = explode("|", $this->recordInfo['functions']);
			$outputStr = "";
			foreach($parts as $row){
				preg_match("/([\w\s]+)\((\w+)\)/", $row, $matches);
				
				if(count($matches) == 3){
					$outputStr .= $matches[1] . ", ";
				}
			}
			
			if(substr($outputStr, (strlen($outputStr) - 2), 2) === ", "){
				$outputStr = substr($outputStr, 0, (strlen($outputStr)  - 2));
			}
			
			return $outputStr;
		} else {
			return stripslashes($this->recordInfo['functions']);
		}
	}
	
	public function setFunctions($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['functions'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	public function getDefaultUrl(){
		return stripslashes($this->recordInfo['defaultUrl']);
	}
	
	public function setDefaultUrl($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['defaultUrl'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	public function getSystemFunction($text = false){
		if($text == true){
			return self::getYesNoText($this->recordInfo['systemFunction']);
		} else {
			return (isset($this->recordInfo['systemFunction']) ? $this->recordInfo['systemFunction'] : 0);
		}
	}
	
	public function getActive($text = false){
		if($text == true){
			return self::getYesNoText($this->recordInfo['active']);
		} else {
			return (isset($this->recordInfo['active']) ? $this->recordInfo['active'] : 0);
		}
	}
	
	public function setActive($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['active'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	public function canDelete(){
		return true;
	}
	
	public function delete(){
		global $db; 
		if(self::getId() !== "" && self::canDelete() == true){
			$result = $db->update("DELETE FROM components WHERE name='" . addslashes(self::getId()) . "'");
				
			if($result == true){
				Log::action("Component (" . self::getId() . ") Deleted");
				return $result;
			}
		}
		
		return false;
	}
	
	// TODO: Redefine return to true or false. Optionally enter pointer to store number of records modified. See ConfigOption
	public function save(){
		global $db;
		
		if($this->update == true){
			$result = $db->update("UPDATE components SET " . 
				"name='" . addslashes(self::getName()) . "', " . 
				"displayName='" . addslashes(self::getDisplayName()) . "', " . 
				"functions='" . addslashes(self::getFunctions()) . "', " . 
				"defaultUrl='" . addslashes(self::getDefaultUrl()) . "', " . 
				"systemFunction='" . addslashes(self::getSystemFunction()) . "', " . 
				"active='" . addslashes(self::getActive()) . "' " . 
				"WHERE name='" . addslashes(self::getId()) . "'");
				
			if($result == true){
				$changes = "";
				self::determineClassChanges($this, $changes, $this->recordText);
				
				Log::action("Component (" . self::getId() . ") edited: " . ($changes != "" ? $changes : "None"));
			}
		} else {
			// Check if the ID is unique in the database
			$rowsCount = $db->fetchObject("SELECT name FROM components WHERE name=''");
			$db->fetchObjectDestroy();
			
			if($rowsCount == 0){
				$result = $db->insert("INSERT INTO components (name, displayName, functions, defaultUrl, systemFunction, active) VALUES (" . 
						"'" . addslashes(self::getName()) . "', " . 
						"'" . addslashes(self::getDisplayName()) . "', " . 
						"'" . addslashes(self::getFunctions()) . "', " . 
						"'" . addslashes(self::getDefaultUrl()) . "', " . 
						"'" . addslashes(self::getSystemFunction()) . "', " . 
						"'" . addslashes(self::getActive()) . "')");
				
				if($result == true){
					self::setId($db->getLastInsertId());
					Log::action("Component (" . self::getId() . ") added");
				}
			}
		}
		
		return $result;
	}
	
	public function display(){
		self::displayInfo($this, $this->recordText, $this->recordGets);
	}
	
	public static function exists($inputName){
		global $db;
		
		if(isset($inputName) && strlen($inputName) > 0){
			$rowsCount = $db->fetchObject("SELECT name FROM components WHERE name='" . $inputName . "'");
			$db->fetchObjectDestroy();
			
			if($rowsCount > 0){
				return true;
			}
		}
		
		return false;
	}
}
?>
