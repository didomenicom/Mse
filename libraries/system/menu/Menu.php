<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

class Menu extends Library {
	protected $recordInfo = array('id' => 0);
	private $recordText = array('id' => "ID");
	private $recordGets = array('id' => "getId");
	
	public function Menu($inputId = 0){
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
			$info = $db->fetchAssoc("SELECT * FROM menu WHERE id=" . self::getId());
			
			$this->recordInfo['parent'] = $info->parent;
			$this->recordText['parent'] = "Parent";
			$this->recordGets['parent'] = "getParent";
			$this->recordInfo['name'] = $info->name;
			$this->recordText['name'] = "Name";
			$this->recordGets['name'] = "getName";
			$this->recordInfo['position'] = $info->position;
			$this->recordText['position'] = "Position";
			$this->recordGets['position'] = "getPosition";
			$this->recordInfo['ordering'] = $info->ordering;
			$this->recordText['ordering'] = "Order";
			$this->recordGets['ordering'] = "getOrder";
			$this->recordInfo['internal'] = $info->internal;
			$this->recordText['internal'] = "Internal";
			$this->recordGets['internal'] = "getInternal";
			$this->recordInfo['url'] = $info->url;
			$this->recordText['url'] = "URL";
			$this->recordGets['url'] = "getUrl";
			$this->recordInfo['permissionGroup'] = $info->permissionGroup;
			$this->recordText['permissionGroup'] = "Permission Group";
			$this->recordGets['permissionGroup'] = "getPermissionGroup";
		}
	}
	
	public function getParent($text = 0){
		if($text == 1){
			$parentId = self::getParent();
			
			if($parentId > 0){
				$parent = new Menu($parentId);
				
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
	
	public function getPosition($text = 0){
		if($text == 1){
			ImportClass("Menu.MenuPosition");
			$menuPosition = new MenuPosition(self::getPosition());
			return $menuPosition->getName(1);
		} else {
			return $this->recordInfo['position'];
		}
	}
	
	public function setPosition($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['position'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	public function getOrdering($text = 0){
		if($text == 1){
			return stripslashes($this->recordInfo['ordering']);
		} else {
			return $this->recordInfo['ordering'];
		}
	}
	
	public function setOrdering($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['ordering'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	public function getInternal($text = 0){
		if($text == 1){
			return self::getYesNoText($this->recordInfo['internal']);
		} else {
			return $this->recordInfo['internal'];
		}
	}
	
	public function setInternal($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['internal'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	public function getUrl($text = 0){
		if($text == 1){
			return stripslashes($this->recordInfo['url']);
		} else {
			return $this->recordInfo['url'];
		}
	}
	
	public function setUrl($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['url'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	public function getPermissionGroup($text = 0, $indx = -1){
		if($text == 1){
			if($indx != -1){
				$parts = explode("|", $this->recordInfo['permissionGroup']);
				for($i = 0; $i < count($parts); $i++){
					if($parts[$i] > 0){
						ImportClass("Group.Group");
						$permissionGroup = new Group($parts[$i]);
						return $permissionGroup->getName(1);
					} elseif(self::getPermissionGroup() == -1){
						return "Guest";
					}
				}
			} else {
				$output = "";
				$parts = explode("|", $this->recordInfo['permissionGroup']);
				for($i = 0; $i < count($parts); $i++){
					if($parts[$i] > 0){
						ImportClass("Group.Group");
						$permissionGroup = new Group($parts[$i]);
						$output .= $permissionGroup->getName(1) . ", ";
					} elseif(self::getPermissionGroup() == -1){
						$output .= "Guest, ";
					}
				}
				
				$output = self::trimEndOfString($output, ", ");
				
				return $output;
			}
		} else {
			if($indx != -1){
				$parts = explode("|", $this->recordInfo['permissionGroup']);
				for($i = 0; $i < count($parts); $i++){
					if($i == $indx){
						return $parts[$i];
					}
				}
				
				return NULL;
			} else {
				return $this->recordInfo['permissionGroup'];
			}
		}
	}
	
	public function setPermissionGroup($inputValue, $indx = -1){
		if(isset($inputValue)){
			$this->recordInfo['permissionGroup'] = implode("|", $inputValue);
			return true;
		}
		
		return false;
	}
	
	public function canDelete(){
		global $db;
		
		$check = $db->fetchObject("SELECT * FROM menu WHERE parent=" . self::getId());
		$db->fetchObjectDestroy();
		
		return ($check == 0 ? true : false);
	}
	
	public function delete(){
		global $db;
		if(self::getId() > 0 && self::canDelete() == true){
			return $db->delete("DELETE FROM menu WHERE id=" . self::getId());
		}
		
		return false;
	}
	
	public function save(){
		global $db;
		if($this->recordInfo['id'] > 0){
			$result = $db->update("UPDATE menu SET " . 
				"name='" . addslashes(self::getName()) . "', " . 
				"parent='" . addslashes(self::getParent()) . "', " . 
				"position='" . addslashes(self::getPosition()) . "', " . 
				"ordering='" . addslashes(self::getOrdering()) . "', " . 
				"permissionGroup='" . addslashes(self::getPermissionGroup()) . "', " . 
				"url='" . addslashes(self::getUrl()) . "', " . 
				"internal='" . addslashes(self::getInternal()) . "' " . 
				"WHERE id=" . addslashes(self::getId()));
		
			if($result == true){
				$changes = "";
				self::determineClassChanges($this, $changes, $this->recordText);
				
				Log::action("Menu (" . self::getId() . ") edited: " . ($changes != "" ? $changes : "None"));
			}
		} else {
			$result = $db->insert("INSERT INTO menu (name, parent, position, ordering, permissionGroup, internal, url) VALUES (" . 
				"'" . addslashes(self::getName()) . "', " . 
				"'" . addslashes(self::getParent()) . "', " . 
				"'" . addslashes(self::getPosition()) . "', " . 
				"'" . addslashes(self::getOrdering()) . "', " . 
				"'" . addslashes(self::getPermissionGroup()) . "', " . 
				"'" . addslashes(self::getInternal()) . "', " . 
				"'" . addslashes(self::getUrl()) . "')");
				
			if($result == true){
				self::setId($db->getLastInsertId());
				Log::action("Menu (" . self::getId() . ") added");
			}
		}
		
		return $result;
	}
	
	public function display(){
		self::displayInfo($this, $this->recordText, $this->recordGets);
	}
}
?>
