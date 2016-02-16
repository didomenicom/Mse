<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

// TODO: Move the logic to loop through a config into here (ex: Reservation System Location Edit)
class ConfigOption extends Library {
	protected $recordInfo = array('id' => 0);
	private $recordText = array('id' => "ID");
	private $recordGets = array('id' => "getId");
	
	/**
	 * This function can accept an id of an index the database or
	 * an array in the format array("component" => componentName, "name" => configName)
	 * TODO: Cleanup
	 */
	public function ConfigOption($inputId = NULL){
		global $db;
		
		if(is_array($inputId)){
			if(isset($inputId['component']) && isset($inputId['name'])){
				$info = $db->fetchAssoc("SELECT * FROM config WHERE indx='" . $inputId['name'] . "' AND component='" . $inputId['component'] . "'");
					
				if(isset($info->id) && $info->id > 0){
					self::setId($info->id);
				}
			}
		} elseif(isset($inputId) && $inputId > 0){
			self::setId($inputId);
		}
	}
	
	/**
	 * Sets the config id and builds the data array
	 */
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
	
	/**
	 * Returns the config id
	 */
	public function getId(){
		return $this->recordInfo['id'];
	}
	
	/**
	 * Builds the data array from the info stored in the database
	 */
	private function buildData(){
		global $db;
		if(self::getId() > 0){
			$info = $db->fetchAssoc("SELECT * FROM config WHERE id=" . self::getId());
			
			$this->recordInfo['component'] = $info->component;
			$this->recordText['component'] = "Component";
			$this->recordGets['component'] = "getComponent";
			$this->recordInfo['type'] = $info->type;
			$this->recordText['type'] = "Type";
			$this->recordGets['type'] = "getType";
			$this->recordInfo['options'] = $info->options;
			$this->recordText['options'] = "Options";
			$this->recordGets['options'] = "getOptions";
			$this->recordInfo['indx'] = $info->indx;
			$this->recordText['indx'] = "Indx";
			$this->recordGets['indx'] = "getIndx";
			$this->recordInfo['name'] = $info->name;
			$this->recordText['name'] = "Name";
			$this->recordGets['name'] = "getName";
			$this->recordInfo['value'] = $info->value;
			$this->recordText['value'] = "Value";
			$this->recordGets['value'] = "getValue";
			$this->recordInfo['comment'] = $info->comment;
			$this->recordText['comment'] = "Comment";
			$this->recordGets['comment'] = "getComment";
			$this->recordInfo['deleteCheck'] = $info->deleteCheck;
			$this->recordText['deleteCheck'] = "Delete Check";
			$this->recordGets['deleteCheck'] = "getDeleteCheck";
		}
	}
	
	/**
	 * Returns the config item component name
	 */
	public function getComponent($text = 0){
		if($text == 1){
			if($this->recordInfo['component'] !== ""){
				ImportClass("Component.Component");
				
				$component = new Component($this->recordInfo['component']);
				return $component->getName();
			}
		} else {
			return $this->recordInfo['component'];
		}
	}
	
	/**
	 * Sets the config item component name
	 */
	public function setComponent($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['component'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	/**
	 * Returns the config item type
	 */
	public function getType($text = 0){
		if($text == 1){
			ImportClass("Config.ConfigTypes");
			
			return stripslashes(ConfigTypes::getName($this->recordInfo['type']));
		} else {
			return $this->recordInfo['type'];
		}
	}
	
	/**
	 * Sets the config item type
	 */
	public function setType($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['type'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	/**
	 * Returns the config item options
	 * It will return a single item or an array of multiple items on success or NULL if there is an error
	 */
	public function getOptions($text = 0){
		if($text == 1){
			switch(self::getType()){
				case 1: // Integer
					preg_match('/MaxLength=(\d+)/', $this->recordInfo['options'], $matches);
					
					if(count($matches) > 0){
						return $matches[1];
					}
					break;
				case 2: // Double
					preg_match('/MaxLength=(\d+)\|DecimalLength=(\d+)/', $this->recordInfo['options'], $matches);
					
					if(count($matches) > 0){
						return array($matches[1], $matches[2]);
					}
					break;
				case 3: // Array
					return $this->recordInfo['options'];
					break;
				case 4: // TextBox
					preg_match('/MaxLength=(\d+)/', $this->recordInfo['options'], $matches);
					
					if(count($matches) > 0){
						return $matches[1];
					}
					break;
				case 5: // TextArea
					preg_match('/WYSIWYG=(\d+)/', $this->recordInfo['options'], $matches);
						
					if(count($matches) > 0){
						return $matches[1];
					}
					break;
				case 6: // Date
					return $this->recordInfo['options'];
					break;
				case 7: // Option
					return $this->recordInfo['options'];
					break;
				case 8: // True/False
					return $this->recordInfo['options'];
					break;
				default:
					// TODO: Handle unknown type
					print_r("Unknown");
					break;
			}
			
			return NULL;
		} else {
			if(isset($this->recordInfo['options'])){
				return $this->recordInfo['options'];
			}
		}
	}
	
	/**
	 * Sets the config item options
	 */
	public function setOptions($inputValue){
		if(isset($inputValue) && is_array($inputValue)){
			switch(self::getType()){
				case 1: // Integer
					$this->recordInfo['options'] = "MaxLength=" . $inputValue[0];
					break;
				case 2: // Double
					$this->recordInfo['options'] = "MaxLength=" . $inputValue[0] . "|DecimalLength=" . $inputValue[1];
					break;
				case 3: // Array
					$this->recordInfo['options'] = $inputValue[0];
					break;
				case 4: // TextBox
					$this->recordInfo['options'] = "MaxLength=" . $inputValue[0];
					break;
				case 5: // TextArea
					$this->recordInfo['options'] = "WYSIWYG=" . $inputValue[0];
					break;
				case 6: // Date
					$this->recordInfo['options'] = $inputValue[0];
					break;
				case 7: // Option
					$this->recordInfo['options'] = $inputValue[0];
					break;
				case 8: // True/False
					$this->recordInfo['options'] = $inputValue[0];
					break;
				default:
					// TODO: Handle unknown type
					break;
			}
			return true;
		}
		
		return false;
	}
	
	/**
	 * Returns the config item index
	 */
	public function getIndex($text = 0){
		if($text == 1){
			return stripslashes($this->recordInfo['indx']);
		} else {
			if(isset($this->recordInfo['indx'])){
				return $this->recordInfo['indx'];
			}
		}
	}
	
	/**
	 * Sets the config item index
	 */
	public function setIndex($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['indx'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	/**
	 * Returns the config item name
	 */
	public function getName($text = 0){
		if($text == 1){
			return stripslashes($this->recordInfo['name']);
		} else {
			return $this->recordInfo['name'];
		}
	}
	
	/**
	 * Sets the config item name
	 */
	public function setName($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['name'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	/**
	 * Returns the value of the config item
	 */
	public function getValue($text = 0){
		if($text == 1){
			return stripslashes(isset($this->recordInfo['value']) ? $this->recordInfo['value']: "");
		} else {
			return (isset($this->recordInfo['value']) ? $this->recordInfo['value'] : "");
		}
	}
	
	/**
	 * Sets the value of the config item
	 */
	public function setValue($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['value'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	/**
	 * Returns the comment
	 */
	public function getComment($text = 0){
		if($text == 1){
			return stripslashes($this->recordInfo['comment']);
		} else {
			return $this->recordInfo['comment'];
		}
	}
	
	/**
	 * Sets the comment
	 */
	public function setComment($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['comment'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	/**
	 * Returns the path to the code for delete checking
	 */
	public function getDeleteCheck($text = 0){
		if($text == 1){
			return stripslashes($this->recordInfo['deleteCheck']);
		} else {
			return $this->recordInfo['deleteCheck'];
		}
	}
	
	/**
	 * Sets the path to the code for delete checking
	 */
	public function setDeleteCheck($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['deleteCheck'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	/**
	 * Determines if the config item can be deleted
	 * At this point there is nothing that will completely break if it is deleted
	 * so the result is always true
	 * TODO: Implement delete check
	 */
	public function canDelete(){
		return true;
	}
	
	/**
	 * Deletes the item from the database
	 */
	public function delete(){
		global $db;
		
		if(self::getId() > 0 && self::canDelete() == true){
			return $db->delete("DELETE FROM config WHERE id=" . self::getId());
		}
		
		return false;
	}
	
	/**
	 * Stores the data in the database
	 * TODO: Redefine return to true or false. Optionally enter pointer to store number of records modified. 
	 */
	public function save(){
		global $db;
		if($this->recordInfo['id'] > 0){
			$result = $db->update("UPDATE config SET " . 
				"component='" . addslashes(self::getComponent()) . "', " . 
				"type='" . addslashes(self::getType()) . "', " . 
				"options='" . addslashes(self::getOptions()) . "', " . 
				"indx='" . addslashes(self::getIndex()) . "', " . 
				"name='" . addslashes(self::getName()) . "', " . 
				"value='" . addslashes(self::getValue()) . "', " . 
				"deleteCheck='" . addslashes(self::getDeleteCheck()) . "' " . 
				"WHERE id=" . addslashes(self::getId()));
			
			if($result == 1){
				$changes = "";
				self::determineClassChanges($this, $changes, $this->recordText);
				
				Log::action("Config (" . self::getId() . ") edited: " . ($changes != "" ? $changes : "None"));
			} else {
				// Nothing was updated. Check if this was a failure or no updates needed
				$changes = "";
				self::determineClassChanges($this, $changes, $this->recordText);
				
				$result = ($changes === "" ? true : false);
			}
		} else {
			$result = $db->insert("INSERT INTO config (component, type, options, indx, name, value, deleteCheck) VALUES (" . 
				"'" . addslashes(self::getComponent()) . "', " . 
				"'" . addslashes(self::getType()) . "', " . 
				"'" . addslashes(self::getOptions()) . "', " . 
				"'" . addslashes(self::getIndex()) . "', " . 
				"'" . addslashes(self::getName()) . "', " . 
				"'" . addslashes(self::getValue()) . "', " . 
				"'" . addslashes(self::getDeleteCheck()) . "')");
			
			if($result == 1){
				Log::action("Config (" . self::getId() . ") added");
			}
		}
		
		return $result;
	}
	
	/**
	 * Displays all of the data for a config item
	 */
	public function display(){
		self::displayInfo($this, $this->recordText, $this->recordGets);
	}
	
	/**
	 * 
	 */
	private function getTypeText($inputId){
		switch($inputId){
			default:
				return "Unknown";
				break;
		}
	}
	
	/**
	 * Determines if a config option exists based on the compontent
	 * It stores the id of the config in returnId
	 * Returns true if it exists or false otherwise
	 */
	public static function exists($component, $id, $returnId = NULL){
		global $db;
		
		if(isset($component) && strlen($component) > 0 && isset($id) && ($id > 0 || strlen($id) > 0)){
			$info = $db->fetchAssoc("SELECT * FROM config WHERE component='" . $component . "' AND (id='" . $id . "' OR indx='" . $id . "')");
			
			if(isset($info->id) && $info->id == $id){
				$returnId = $info->id;
				return true;
			}
			
			// id was not the id but most likely an index value
			if(isset($info->indx) && $info->indx == $id){
				$returnId = $info->id;
				return true;
			}
		}
		
		return false;
	}
}
?>
