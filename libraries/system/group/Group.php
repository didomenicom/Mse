<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

ImportClass("Library");
Log::setDisplayErrorPage(true);

class Group extends Library {
	protected $recordInfo = array('id' => 0);
	private $recordText = array('id' => "ID");
	private $recordGets = array('id' => "getId");
	
	public function Group($inputId = 0){
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
			$info = $db->fetchAssoc("SELECT * FROM permissionGroups WHERE id=" . self::getId());
			
			$this->recordInfo['name'] = $info->name;
			$this->recordText['name'] = "Name";
			$this->recordGets['name'] = "getName";
			$this->recordInfo['parent'] = $info->parent;
			$this->recordText['parent'] = "Parent";
			$this->recordGets['parent'] = "getParent";
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
			return 1;
		}
		
		return 0;
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
			return 1;
		}
		
		return 0;
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
			return 1;
		}
		
		return 0;
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
				"active='" . addslashes(self::getActive()) . "' " . 
				"WHERE id=" . addslashes(self::getId()));
				
			if($result == true){
				$changes = "";
				self::determineClassChanges($this, $changes, $this->recordText);
				
				Log::action("Permission Group (" . self::getId() . ") edited: " . ($changes != "" ? $changes : "None"));
			}
		} else {
			$result = $db->insert("INSERT INTO permissionGroups (name, parent, active) VALUES (" . 
				"'" . addslashes(self::getName()) . "', " . 
				"'" . addslashes(self::getParent()) . "', " . 
				"'" . addslashes(self::getActive()) . "')");
				
			if($result == true){
				Log::action("Permission Group (" . self::getId() . ") added");
			}
		}
		
		return $result;
	}
	
	public function display(){
		self::displayInfo($this, $this->recordText, $this->recordGets);
	}
}
?>