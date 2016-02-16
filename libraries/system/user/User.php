<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

// TODO: Check if user exists before adding
class User extends Library {
	protected $recordInfo = array('id' => 0);
	private $recordText = array('id' => "ID");
	private $recordGets = array('id' => "getId");
	protected $dynamicFieldRecord = array('component' => "users", 'name' => "dynamicFields");
	
	public function User($inputId = 0){
		ImportClass("User.Settings");
		
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
			$this->recordInfo['dynamicFields'] = $info->dynamicFields;
			$this->recordText['dynamicFields'] = "Fields";
			$this->recordGets['dynamicFields'] = "getDynamicFields(1)";
			$this->recordInfo['settings'] = $info->settings;
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
	
	/**
	 * 
	 */
	public function getSetting($inputName){
		if(isset($inputName)){
			// Create a new settings handler
			$settings = new Settings($this->recordInfo['settings']);
			return $settings->getSetting($inputName);
		}
	}
	
	/**
	 * 
	 */
	private function getSettings(){
		return (isset($this->recordInfo['settings']) ? $this->recordInfo['settings'] : "");
	}
	
	/**
	 * 
	 */
	public function setSettings($inputName, $inputValue){
		if(isset($inputName)){
			// Create a new settings handler
			$settings = new Settings($this->recordInfo['settings']);
			$settings->setSetting($inputName, $inputValue);
			$this->recordInfo['settings'] = $settings->writeSettings();
			return 1;
		}
		
		return 0;
	}
	
	/**
	 * 
	 */
	public function saveSettings(){
		global $db; 
		
		if($this->recordInfo['id'] > 0){
			$result = $db->update("UPDATE users SET settings='" . addslashes(self::getSettings()) . "' WHERE id=" . addslashes(self::getId()));
			
			if($result == true){
				return true;
			} else {
				print_r("A");
				die();
			}
		}
		
		return false;
	}
	
	public function getRegistered($text = 0){
		if($text == 1){
			if(isset($this->recordInfo['registered'])){
				return $this->recordInfo['registered'];
			} else {
				return Date::getDbDateTimeFormat();
			}
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
		if($text == 1){
			if(isset($this->recordInfo['lastLogin'])){
				return $this->recordInfo['lastLogin'];
			} else {
				return Date::getDbDateTimeFormat();
			}
		} else {
			return stripslashes($this->recordInfo['lastLogin']);
		}
	}
	
	public function setLastLogin($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['lastLogin'] = $inputValue;
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
			$changes = "";
			self::determineClassChanges($this, $changes, $this->recordText);
			
			$result = $db->update("UPDATE users SET " . 
				"name='" . addslashes(self::getName()) . "', " . 
				"username='" . addslashes(self::getUsername()) . "', " . 
				"email='" . addslashes(self::getEmail()) . "', " . 
				"password='" . addslashes(self::getPassword()) . "', " . 
				"permissionGroup='" . addslashes(self::getPermissionGroup()) . "', " . 
				"receiveEmail='" . addslashes(self::getReceiveEmail()) . "', " . 
				"dynamicFields='" . addslashes(self::getDynamicFields()) . "', " . 
				"settings='" . addslashes(self::getSettings()) . "', " . 
				"lastLogin='" . addslashes(self::getLastLogin()) . "', " . 
				"deleted='" . addslashes(self::getDeleted()) . "' " . 
				"WHERE id=" . addslashes(self::getId()));
				
			if($result == false){
				Log::error("User (" . self::getId() . ") failed to edit: " . ($changes != "" ? $changes : "None"));
			}
		} else {
			$result = $db->insert("INSERT INTO users (name, username, email, password, permissionGroup, receiveEmail, dynamicFields, settings, registered, lastLogin, deleted) VALUES (" . 
				"'" . addslashes(self::getName()) . "', " . 
				"'" . addslashes(self::getUsername()) . "', " . 
				"'" . addslashes(self::getEmail()) . "', " . 
				"'" . addslashes(self::getPassword()) . "', " . 
				"'" . addslashes(self::getPermissionGroup()) . "', " . 
				"'" . addslashes(self::getReceiveEmail()) . "', " . 
				"'" . addslashes(self::getDynamicFields()) . "', " .
				"'" . addslashes(self::getSettings()) . "', " . 
				"'" . Date::getDbDateTimeFormat() . "', " . 
				"'0', " . 
				"'0')");
				
			if($result == true){
				self::setId($db->getLastInsertId(), false);
				Log::action("User (" . self::getId() . ") added");
			} else {
				Log::error("User failed to add.");
			}
		}
		
		return $result;
	}
	
	public function display(){
		self::displayInfo($this, $this->recordText, $this->recordGets);
	}
	
	/**
	 * Checks if a user has permission for a given permission group
	 * Usage: 
	 * Input: 
	 * Output: True if they have permission, false otherwise
	 * TODO: Make more efficient
	 */
	public function hasAccess($inputPermissionGroup){
		ImportClass("Group.Group");
		
		// Check if this is guest access
		if($inputPermissionGroup == -1){
			return true;
		}
		
		if($inputPermissionGroup > 0){
			// Do a quick check to see if the inputPermission group is the same as 
			// The current users permission group
			if(self::getPermissionGroup() == $inputPermissionGroup){
				return true;
			}
			
			// Time to dig a bit deeper... 
			// See if one of the the inputPermissionGroup(s) is a parent or child of this users group
			$inputPermissionGroupParts = explode("|", $inputPermissionGroup);
			
			foreach($inputPermissionGroupParts as $permissionGroup){
				$checkResult = Group::determineRelationship(self::getPermissionGroup(), $permissionGroup);
				if($checkResult == 0){
					// They are the same... this should not happen
					// TODO: Throw error
				} elseif($checkResult == 1){
					// The current user is a parent of the inputPermissionGroup
					return true;
				} elseif($checkResult == -1){
					// The current user is a child of the inputPermissionGroup
				}
			}
		}
		
		return false;
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
		} elseif(isset($this->recordInfo['email']) && self::getEmail() !== ""){
			// Email is filled in -- check the db
			$result = $db->fetchAssoc("SELECT id FROM users WHERE email='" . self::getEmail() . "' AND deleted=0");
			
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
	 * 
	 */
	public function sendUserEmail(){
		ImportClass("Mailer.Mailer");
		
		if(self::getId() == 0){
			// This is add user
			
			if(ConfigOption::exists("users", "newUserEmailSubject")){
				// Get the mailer
				$mailer = new Mailer();
				
				$emailSubject = new ConfigOption(array("component" => "users", "name" => "newUserEmailSubject"));
				$emailBody = new ConfigOption(array("component" => "users", "name" => "newUserEmailBody"));
				
				// Available 'filters'
				$findReplaceArray = array(
						'date' => date("m/d/Y"),
						'name' => self::getName(1),
						'username' => self::getUsername(1),
						'email' => self::getEmail(1),
						'password' => Encryption::decrypt(self::getPassword(1)), // TODO: Security hole
						'permissiongroup' => self::getPermissionGroup(1),
						'receiveemails' => self::getReceiveEmail(1),
						'registerdate' => self::getRegistered(1),
						'lastlogin' => self::getLastLogin(1)
				);
				
				// Process and add them
				$mailer->setSubject(self::findReplaceEmailString($emailSubject->getValue(), $findReplaceArray));
				$mailer->setMessage(self::findReplaceEmailString(nl2br($emailBody->getValue()), $findReplaceArray));
				
				// Add the receipient
				$mailer->setTo(self::getName(1), self::getEmail(1));
				
				// Send the email
				if($mailer->send() == true){
					// Success
				} else {
					// TODO: Error
				}
				
				return true;
			}
		} else {
			// This is edit user 
			
			if(ConfigOption::exists("users", "editUserEmailSubject")){
				// Get the mailer
				$mailer = new Mailer();
				
				$emailSubject = new ConfigOption(array("component" => "users", "name" => "editUserEmailSubject"));
				$emailBody = new ConfigOption(array("component" => "users", "name" => "editUserEmailBody"));
				
				// Available 'filters'
				$findReplaceArray = array(
						'date' => date("m/d/Y"),
						'name' => self::getName(1),
						'username' => self::getUsername(1),
						'email' => self::getEmail(1),
						'password' => self::getPassword(1), // TODO: Security hole
						'permissiongroup' => self::getPermissionGroup(1),
						'receiveemails' => self::getReceiveEmail(1),
						'registerdate' => self::getRegistered(1),
						'lastlogin' => self::getLastLogin(1)
				);
				
				// Process and add them
				$mailer->setSubject(self::findReplaceEmailString($emailSubject->getValue(), $findReplaceArray));
				$mailer->setMessage(self::findReplaceEmailString($emailBody->getValue(), $findReplaceArray));
				
				// Add the receipient
				$mailer->setTo(self::getName(1), self::getEmail(1));
				
				// Send the email
				if($mailer->send() == true){
					// Success
				} else {
					// TODO: Error
				}
				
				return true;
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
				ImportClass("Group.Group");
				
				while($db->fetchObjectHasNext() == true){
					$row = $db->fetchObjectGetNext();
					
					// TODO: Check permissions
					$user = new User($row->id);
					
					if(UserFunctions::getLoggedIn()->hasAccess($user->getPermissionGroup())){
						array_push($outputArray, $row->id);
					}
				}
				
				$db->fetchObjectDestroy();
			}
			
			return $outputArray;
		}
	}
}
?>
