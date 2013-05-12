<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

ImportClass("Library");
Log::setDisplayErrorPage(true);

/**
 * TODO: Add multi dimensional options for config (Ex: User options -> type). Only available if parent type is array
 */
class Component extends Library {
	protected $recordInfo = array('name' => "");
	private $recordText = array('id' => "ID");
	private $recordGets = array('id' => "getId");
	
	public function Component($inputId = 0){
		self::setId($inputId);
	}
	
	// TODO: Test name as primary key/index
	public function setId($inputId, $buildData = 0){
		if(isset($inputId) && $inputId !== ""){
			// Check if the key is valid (all lower case characters and '-')
			if(preg_match("/([a-z\-]+)/", $inputId)){
				$this->recordInfo['name'] = $inputId;
					
				if($buildData == 0){
					self::buildData();
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
			
			$this->recordInfo['displayName'] = $info->displayName;
			$this->recordText['displayName'] = "Name";
			$this->recordGets['displayName'] = "getName";
		}
	}
	
	public function getName(){
		return stripslashes($this->recordInfo['displayName']);
	}
	
	public function setName($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['displayName'] = $inputValue;
			return 1;
		}
		
		return 0;
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
		if(self::getId() !== ""){
			$result = $db->update("UPDATE components SET " . 
				"name='" . addslashes(self::getName()) . "' " . 
				"WHERE name='" . addslashes(self::getId()) . "'");
				
			if($result == true){
				$changes = "";
				self::determineClassChanges($this, $changes, $this->recordText);
				
				Log::action("Component (" . self::getId() . ") edited: " . ($changes != "" ? $changes : "None"));
			}
		} else {
			// Check if the ID is unique in the database
			$rowsCount = $db->fetchObject("SELECT id FROM components WHERE name=''");
			
			if($rowsCount == 0){
				$db->fetchObjectDestroy();
				
				$result = $db->insert("INSERT INTO components (name, displayName) VALUES (" . 
						"'" . addslashes(self::getId()) . "', " . 
						"'" . addslashes(self::getName()) . "')");
				
				if($result == true){
					Log::action("Component (" . self::getId() . ") added");
				}
			}
		}
		
		return $result;
	}
	
	public function display(){
		self::displayInfo($this, $this->recordText, $this->recordGets);
	}
}
?>