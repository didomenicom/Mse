<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

class Group extends Library {
	protected $recordInfo = array('id' => 0);
	private $recordText = array('id' => "ID");
	private $recordGets = array('id' => "getId");
	
	public function Group($inputId = 0){
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
			$info = $db->fetchAssoc("SELECT * FROM permissionGroups WHERE id=" . self::getId());
			
			$this->recordInfo['name'] = $info->name;
			$this->recordText['name'] = "Name";
			$this->recordGets['name'] = "getName";
			
			$this->recordInfo['parent'] = $info->parent;
			$this->recordText['parent'] = "Parent";
			$this->recordGets['parent'] = "getParent";
			
			$this->recordInfo['componentFunctions'] = $info->componentFunctions; // TODO: Use the permissionTable DB Table? 
			$this->recordText['componentFunctions'] = "Component Functions";
			$this->recordGets['componentFunctions'] = "getComponentFunctions";
			
			$this->recordInfo['active'] = $info->active;
			$this->recordText['active'] = "Active";
			$this->recordGets['active'] = "getActive";
		}
	}
	
	public function getName($text = 0){
		if(isset($this->recordInfo['name'])){
			if($text == 1){
				return stripslashes($this->recordInfo['name']);
			} else {
				return $this->recordInfo['name'];
			}
		}
	}
	
	public function setName($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['name'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	public function getParent($text = 0){
		if($text == 1){
			$parentId = self::getParent();
			
			if($parentId > 0){
				$parent = new Group($parentId);
				
				if($parent->getId() > 0){
					return $parent->getName(1);
				}
				
				return "Unknown";
			} else {
				return "None";
			}
		} else {
			return $this->recordInfo['parent'];
		}
	}
	
	public function setParent($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['parent'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	/**
	 * This function finds and returns the value set for a specific component function. 
	 * If the function is not defined, it will return false
	 * Return true if set, false otherwise
	 */
	public function getComponentFunction($component, $function){
		// Break everything apart
		// Format: component(function=<value>)|component(function=<value>)
		$componentRecords = explode("|", (isset($this->recordInfo['componentFunctions']) ? $this->recordInfo['componentFunctions'] : ""));
		
		foreach($componentRecords as $componentRecord){
			// Break apart record
			preg_match("/(\w+)\((\w+)\=\<(\d)\>\)/", $componentRecord, $matches);
			
			if(count($matches) > 0){
				if($matches[1] === $component){
					if($matches[2] === $function){
						return ($matches[3] == 1 ? true : false);
					}
				}
			}
		}
		
		return false;
	}
	
	/**
	 * This function finds and sets the value set for a specific component function.
	 * If the function is not defined, it will create the record
	 * Return true on success, false otherwise
	 */
	public function setComponentFunction($component, $function, $value){
		if(strlen($component) > 0 && strlen($function) > 0){
			// Loop through the components and see if it exists
			$componentRecords = explode("|", $this->recordInfo['componentFunctions']);
			$resultStr = "";
			$found = false;
			
			foreach($componentRecords as $componentRecord){
				// Break apart record
				preg_match("/(\w+)\((\w+)\=\<(\d)\>\)/", $componentRecord, $matches);
					
				if(count($matches) > 0){
					if($matches[1] === $component){
						if($matches[2] === $function){
							$matches[3] = ($value == 1 ? 1 : 0);
							$found = true;
						}
					}
					
					$resultStr .= $matches[1] . "(" . $matches[2] . "=<" . $matches[3] . ">)|";
				}
			}
			
			if($found == false){
				$resultStr .= $component . "(" . $function . "=<" . $value . ">)|";
			}
			
			if(substr($resultStr, (strlen($resultStr) - 1), 1) === "|"){
				$resultStr = substr($resultStr, 0, (strlen($resultStr) - 1));
			}
			
			$this->recordInfo['componentFunctions'] = $resultStr;
		}
		
		return false;
	}
	
	public function getComponentFunctions($text = 0){
		if($text == 1){
			return stripslashes($this->recordInfo['componentFunctions']);
		} else {
			return $this->recordInfo['componentFunctions'];
		}
	}
	
	public function clearComponentFunctions(){
		$this->recordInfo['componentFunctions'] = "";
		
		return true;
	}
	
	public function getActive($text = 0){
		if($text == 1){
			return self::getYesNoText($this->recordInfo['active']);
		} else {
			return $this->recordInfo['active'];
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
		global $db;
		
		$check = $db->fetchObject("SELECT * FROM permissionGroups WHERE parent=" . self::getId());
		$db->fetchObjectDestroy();
		
		return ($check > 0 ? false : true);
	}
	
	public function delete(){
		global $db;
		if(self::getId() > 0 && self::canDelete() == true){
			return $db->delete("DELETE FROM permissionGroups WHERE id=" . self::getId());
		}
		
		return false;
	}
	
	public function save(){
		global $db;
		if($this->recordInfo['id'] > 0){
			$result = $db->update("UPDATE permissionGroups SET " . 
				"name='" . addslashes(self::getName()) . "', " . 
				"parent='" . addslashes(self::getParent()) . "', " . 
				"componentFunctions='" . addslashes(self::getComponentFunctions()) . "', " . 
				"active='" . addslashes(self::getActive()) . "' " . 
				"WHERE id=" . addslashes(self::getId()));
				
			if($result == true){
				$changes = "";
				self::determineClassChanges($this, $changes, $this->recordText);
				
				Log::action("Permission Group (" . self::getId() . ") edited: " . ($changes != "" ? $changes : "None"));
			}
		} else {
			$result = $db->insert("INSERT INTO permissionGroups (name, parent, componentFunctions, active) VALUES (" . 
				"'" . addslashes(self::getName()) . "', " . 
				"'" . addslashes(self::getParent()) . "', " . 
				"'" . addslashes(self::getComponentFunctions()) . "', " . 
				"'" . addslashes(self::getActive()) . "')");
				
			if($result == true){
				self::setId($db->getLastInsertId());
				Log::action("Permission Group (" . self::getId() . ") added");
			}
		}
		
		return $result;
	}
	
	public function display(){
		self::displayInfo($this, $this->recordText, $this->recordGets);
	}
	
	/**
	 * Figures out the relationship of 2 permission groups
	 * Inputs:
	 * 		currentGroup = current group id
	 * 		checkGroup = check group id
	 * Returns: 
	 * 		2 = no relationship
	 * 		1 = current group is parent of check group
	 * 		0 = both groups are the same
	 * 		-1 = current group is child of check group
	 * 		-100 = ERROR
	 * TODO: Change -100 to throw exception
	 */
	public static function determineRelationship($currentGroup, $checkGroup){
		if(isset($currentGroup) && $currentGroup > 0 && isset($checkGroup) && $checkGroup > 0){
			// 1. Check to see if the groups are the same
			if($currentGroup == $checkGroup){
				// They are the same
				return 0;
			}
			
			// 2. Check to see if currentGroup is the parent of the checkGroup
			// Check to see if checkGroup has a parent
			$checkGroupStruct = new Group($checkGroup);
			
			if($checkGroupStruct->getParent() > 0){
				// Check group has a parent
				// See if the parent is currentGroup
				if($checkGroupStruct->getParent() == $currentGroup){
					// currentGroup is a direct parent of checkGroup
					return 1;
				}
				
				// See if the currentGroup is a "grandparent" of checkGroup
				if(Group::determineRelationship($currentGroup, $checkGroupStruct->getParent()) == 1){
					// Current group is a parent of the check group
					return 1;
				}
			}
			
			// 3. Check to see if checkGroup is the parent of currentGroup
			// Check if currentGroup has a parent
			$currentGroupStruct = new Group($currentGroup);
				
			if($currentGroupStruct->getParent() > 0){
				// See if the parent is checkGroup
				if($currentGroupStruct->getParent() == $checkGroup){
					// checkGroup is a direct parent of currentGroup
					return -1;
				}
				
				if(Group::determineRelationship($checkGroup, $currentGroupStruct->getParent()) == 1){
					// Current group is a child of the check group
					return -1;
				}
			}
			
			// We could not figure out the relationship... so no association 
			return 2;
		}
		
		return -100;
	}
}
?>
