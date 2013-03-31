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

class User extends Library {
	protected $recordInfo = array('id' => 0);
	private $recordText = array('id' => "ID");
	private $recordGets = array('id' => "getId");
	
	public function User($inputId = 0){
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
			// Search the db for the user
			$info = $db->fetchAssoc("SELECT * FROM users WHERE id=" . self::getId());
			
			$this->recordInfo['name'] = $info->name;
			$this->recordText['name'] = "Name";
			$this->recordGets['name'] = "getName";
			$this->recordInfo['username'] = $info->username;
			$this->recordText['username'] = "Username";
			$this->recordGets['username'] = "getUsername";
			$this->recordInfo['email'] = $info->email;
			$this->recordText['email'] = "Email Address";
			$this->recordGets['email'] = "getEmail";
			$this->recordInfo['password'] = $info->password;
			$this->recordInfo['permissionGroup'] = $info->permissionGroup;
			$this->recordText['permissionGroup'] = "Permission Group";
			$this->recordGets['permissionGroup'] = "getPermissionGroup";
			$this->recordInfo['receiveEmail'] = $info->receiveEmail;
			$this->recordText['recieveEmail'] = "Receive Emails";
			$this->recordGets['recieveEmail'] = "getReceiveEmail";
			$this->recordInfo['registered'] = $info->registered;
			$this->recordText['registered'] = "Registered";
			$this->recordGets['registered'] = "getRegistered";
			$this->recordInfo['lastLogin'] = $info->lastLogin;
			$this->recordText['lastLogin'] = "Last Login";
			$this->recordGets['lastLogin'] = "getLastLogin";
			$this->recordInfo['deleted'] = $info->deleted;
			$this->recordText['deleted'] = "Deleted";
			$this->recordGets['deleted'] = "getDeleted";
		}
	}
	
	public function getName(){
		return stripslashes($this->recordInfo['name']);
	}
	
	public function setName($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['name'] = $inputValue;
			return 1;
		}
		
		return 0;
	}
	
	public function getUsername(){
		return stripslashes($this->recordInfo['username']);
	}
	
	public function setUsername($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['username'] = $inputValue;
			return 1;
		}
		
		return 0;
	}
	
	public function getEmail(){
		return stripslashes($this->recordInfo['email']);
	}
	
	public function setEmail($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['email'] = $inputValue;
			return 1;
		}
		
		return 0;
	}
	
	public function getPassword(){
		return stripslashes($this->recordInfo['password']);
	}
	
	public function setPassword($inputValue){
		if(isset($inputValue) && $inputValue !== ""){
			$this->recordInfo['password'] = Encryption::encrypt($inputValue);
			return 1;
		}
		
		return 0;
	}
	
	public function getPermissionGroup($text = 0){
		if($text > 0){
			ImportClass("Group.Group");
			$group = new Group(self::getPermissionGroup());
			
			if($text == 1){
				return stripslashes($group->getName());
			} elseif($text == 2){
				return $group;
			}
		} else {
			return $this->recordInfo['permissionGroup'];
		}
	}
	
	public function setPermissionGroup($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['permissionGroup'] = $inputValue;
			return 1;
		}
		
		return 0;
	}
	
	public function getReceiveEmail($text = 0){
		if($text == 0){
			return stripslashes($this->recordInfo['receiveEmail']);
		} else {
			return self::getYesNoText($this->recordInfo['receiveEmail']);
		}
	}
	
	public function setReceiveEmail($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['receiveEmail'] = $inputValue;
			return 1;
		}
		
		return 0;
	}
	
	public function getRegistered($text = 0){
		if($text == 0){
			return stripslashes($this->recordInfo['registered']);
		} else {
			return $this->recordInfo['registered'];
		}
	}
	
	public function setRegistered($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['registered'] = $inputValue;
			return 1;
		}
		
		return 0;
	}
	
	public function getLastLogin($text = 0){
		if($text == 0){
			return stripslashes($this->recordInfo['lastLogin']);
		} else {
			return $this->recordInfo['lastLogin'];
		}
	}
	
	public function setLastLogin($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['lastLogin'] = $inputValue;
			return 1;
		}
		
		return 0;
	}
	
	public function getDeleted($text = 0){
		if($text == 0){
			return stripslashes($this->recordInfo['deleted']);
		} else {
			return $this->recordInfo['deleted'];
		}
	}
	
	public function setDeleted($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['deleted'] = $inputValue;
			return 1;
		}
		
		return 0;
	}
	
	public function canDelete(){
		return true;
	}
	
	public function delete(){
		if($this->recordInfo['id'] > 0 && self::canDelete() == true){
			self::setDeleted(Date::getDbDateTimeFormat());
			$result = self::save();
			
			if($result == true){
				// Log it
				Log::action("User Deleted by " . UserFunctions::getLoggedIn()->getUsername());
				return true;
			}
		}
		
		return false;
	}
	
	public function save(){
		global $db;
		if($this->recordInfo['id'] > 0){
			$result = $db->update("UPDATE users SET " . 
				"name='" . addslashes(self::getName()) . "', " . 
				"username='" . addslashes(self::getUsername()) . "', " . 
				"email='" . addslashes(self::getEmail()) . "', " . 
				"password='" . addslashes(self::getPassword()) . "', " . 
				"permissionGroup='" . addslashes(self::getPermissionGroup()) . "', " . 
				"receiveEmail='" . addslashes(self::getReceiveEmail()) . "', " . 
				"lastLogin='" . addslashes(self::getLastLogin()) . "', " . 
				"deleted='" . addslashes(self::getDeleted()) . "' " . 
				"WHERE id=" . addslashes(self::getId()));
				
			if($result == true){
				$changes = "";
				self::determineClassChanges($this, $changes, $this->recordText);
				
				Log::action("User (" . self::getId() . ") edited: " . ($changes != "" ? $changes : "None"));
			}
		} else {
			$result = $db->insert("INSERT INTO users (name, username, email, password, permissionGroup, receiveEmail, registered, lastLogin, deleted) VALUES (" . 
				"'" . addslashes(self::getName()) . "', " . 
				"'" . addslashes(self::getUsername()) . "', " . 
				"'" . addslashes(self::getEmail()) . "', " . 
				"'" . addslashes(self::getPassword()) . "', " . 
				"'" . addslashes(self::getPermissionGroup()) . "', " . 
				"'" . addslashes(self::getReceiveEmail()) . "', " . 
				"'" . Date::getDbDateTimeFormat() . "', " . 
				"'0', " . 
				"'0')");
				
			if($result == true){
				Log::action("User (" . self::getId() . ") added");
			}
		}
		
		return $result;
	}
	
	public function display(){
		self::displayInfo($this, $this->recordText, $this->recordGets);
	}
	
	/**
	 * Checks if a user exists in the database. 
	 * Usage: Create a new object and set the username. 
	 * Input: save - saves the user id and pulls all of the data
	 * Output: True if exists, false otherwise
	 * TODO: Make the save more efficient
	 */
	public function userExists($save = false){
		global $db;
		
		if(isset($this->recordInfo['username']) && self::getUsername() !== ""){
			// Username is filled in -- check the db
			$result = $db->fetchAssoc("SELECT id FROM users WHERE username='" . self::getUsername() . "' AND deleted=0");
			
			if(isset($result->id) && $result->id > 0){
				if($save == true){
					if($this->recordInfo['id'] == 0){
						self::setId($result->id);
					}
				}
				
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Checks if a user has a valid password
	 * Usage: Create a new object and set the username and password (encryption set inside user class). 
	 * Input: None
	 * Output: True if correct password, false otherwise
	 */
	public function passwordCorrect($inputPassword){
		global $db;
		
		$inputPassword = Encryption::encrypt($inputPassword);
		if(isset($this->recordInfo['username']) && self::getUsername() !== "" && isset($inputPassword) && $inputPassword !== ""){
			// Username and password is filled in -- check the db
			$result = $db->fetchAssoc("SELECT id, password FROM users WHERE username='" . self::getUsername() . "' AND deleted = 0");
			
			if(isset($result->id) && $result->id > 0){
				// Found the user
				if($inputPassword === $result->password){
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Returns the terms based on the input value
	 */
	public static function search($inputValue, $deleted = false){
		global $db;
		
		if(isset($inputValue) && $inputValue !== ""){
			$inputValue = addslashes($inputValue);
			$outputArray = array();
			
			if($deleted == false){
				$deleted = "deleted='0000-00-00 00:00:00'";
			} else {
				$deleted = "deleted != '0000-00-00 00:00:00'";
			}
			
			$resultCount = $db->fetchObject("SELECT `users`.id FROM `users` " . 
					"LEFT JOIN `permissionGroups` ON `users`.permissionGroup=`permissionGroups`.id WHERE " . $deleted . " AND (" . 
					"`users`.name LIKE '%" . $inputValue . "%' OR " . 
					"`users`.username LIKE '%" . $inputValue . "%' OR " . 
					"`users`.email LIKE '%" . $inputValue . "%' OR " . 
					"`permissionGroups`.name LIKE '%" . $inputValue . "%')");
			
			if($resultCount > 0){
				while($db->fetchObjectHasNext() == true){
					$row = $db->fetchObjectGetNext();
					
					array_push($outputArray, $row->id);
				}
				
				$db->fetchObjectDestroy();
			}
			
			return $outputArray;
		}
	}
}
?>