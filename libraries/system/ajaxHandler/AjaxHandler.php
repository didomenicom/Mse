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

/**
 * TODO: Add access permission for system handlers. Can edit expiration but cannot delete. 
 * Add list of urls that can call the handler
 */
class AjaxHandler extends Library {
	protected $recordInfo = array('id' => 0);
	private $recordText = array('id' => "ID");
	private $recordGets = array('id' => "getId");
	
	public function AjaxHandler($inputId = NULL){
		if(isset($inputId)){
			// A handler might exist, check the db
			self::setId($inputId);
			self::buildData();
		}
	}
	
	/** 
	 * 
	 */ 
	public function getId(){
		if(isset($this->recordInfo['id'])){
			return $this->recordInfo['id'];
		} else {
			return NULL;
		}
	}
	
	/** 
	 * 
	 */ 
	public function setId($inputId){
		if(isset($inputId)){
			$this->recordInfo['id'] = $inputId;
			
			return true;
		}
		
		return false;
	}
	
	private function buildData(){
		global $db;
		
		if(self::getId() > 0){
			// Search the db for the handler
			$info = $db->fetchAssoc("SELECT * FROM ajaxHandler WHERE id='" . self::getId() . "'");
			
			$this->recordInfo['handlerId'] = $info->handlerId;
			$this->recordText['handlerId'] = "Ajax Handler ID";
			$this->recordGets['handlerId'] = "getHandlerId";
			$this->recordInfo['name'] = $info->name;
			$this->recordText['name'] = "Name";
			$this->recordGets['name'] = "getName";
			$this->recordInfo['className'] = $info->className;
			$this->recordText['className'] = "Class Name";
			$this->recordGets['className'] = "getClassName";
			$this->recordInfo['callerFunction'] = $info->callerFunction;
			$this->recordText['callerFunction'] = "Caller Function";
			$this->recordGets['callerFunction'] = "getCallerFunction";
			$this->recordInfo['createTimestamp'] = $info->createTimestamp;
			$this->recordText['createTimestamp'] = "Created";
			$this->recordGets['createTimestamp'] = "getCreateTimestamp";
			$this->recordInfo['expireTimestamp'] = $info->expireTimestamp;
			$this->recordText['expireTimestamp'] = "Expires";
			$this->recordGets['expireTimestamp'] = "getExpireTimestamp";
		}
	}
	
	public function getHandlerId($text = 0){
		return stripslashes(Encryption::encrypt($this->recordInfo['handlerId']));
	}
	
	public function getName($text = 0){
		return stripslashes($this->recordInfo['name']);
	}
	
	public function setName($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['name'] = $inputValue;
			return 1;
		}
		
		return 0;
	}
	
	public function getClassName($text = 0){
		return stripslashes($this->recordInfo['className']);
	}
	
	public function setClassName($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['className'] = $inputValue;
			return 1;
		}
		
		return 0;
	}
	
	public function getCallerFunction($text = 0){
		return stripslashes($this->recordInfo['callerFunction']);
	}
	
	public function setCallerFunction($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['callerFunction'] = $inputValue;
			return 1;
		}
		
		return 0;
	}
	
	public function getCreateTimestamp($text = 0){
		if($text == 1){
			return stripslashes($this->recordInfo['createTimestamp']);
		} else {
			if(isset($this->recordInfo['createTimestamp'])){
				return $this->recordInfo['createTimestamp'];
			} else {
				return Date::getDbDateTimeFormat();
			}
		}
	}
	
	public function setCreateTimestamp($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['createTimestamp'] = $inputValue;
			return 1;
		}
		
		return 0;
	}
	
	public function getExpireTimestamp($text = 0){
		if($text == 1){
			return stripslashes($this->recordInfo['expireTimestamp']);
		} else {
			if(isset($this->recordInfo['expireTimestamp'])){
				return $this->recordInfo['expireTimestamp'];
			} else {
				return 0;
			}
		}
	}
	
	public function setExpireTimestamp($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['expireTimstamp'] = $inputValue;
			return 1;
		}
		
		return 0;
	}
	
	public function canDelete(){ // TODO: Finish
		return true;
	}
	
	public function delete(){
		global $db; 
		
		if($this->recordInfo['id'] > 0 && self::canDelete() == true){
			$result = $db->delete("DELETE FROM ajaxHandler WHERE id=" . addslashes(self::getId()));
			
			if($result == true){
				// Log it
				Log::action("Ajax Handler Deleted by " . UserFunctions::getLoggedIn()->getUsername());
				return true;
			}
		}
		
		return false;
	}
	
	public function save(){
		global $db;
		
		if($this->recordInfo['id'] > 0){
			$result = $db->update("UPDATE ajaxHandler SET " . 
				"name='" . addslashes(self::getName()) . "', " . 
				"className='" . addslashes(self::getClassName()) . "', " . 
				"callerFunction='" . addslashes(self::getCallerFunction()) . "', " . 
				"expireTimestamp='" . addslashes(self::getExpireTimestamp()) . "' " . 
				"WHERE id=" . $this->recordInfo['id']);
				
			if($result == true){
				$changes = "";
				self::determineClassChanges($this, $changes, $this->recordText);
				
				Log::action("User (" . self::getId() . ") edited: " . ($changes != "" ? $changes : "None"));
			}
		} else {
			$result = $db->insert("INSERT INTO ajaxHandler (handlerId, name, className, callerFunction, createTimestamp, expireTimestamp) VALUES (" . 
				"'" . self::generateUniqueId() . "', " . 
				"'" . addslashes(self::getName()) . "', " . 
				"'" . addslashes(self::getClassName()) . "', " . 
				"'" . addslashes(self::getCallerFunction()) . "', " . 
				"'" . addslashes(self::getCreateTimestamp()) . "', " . 
				"'" . addslashes(self::getExpireTimestamp()) . "')");
				
			if($result == true){
				Log::action("Ajax Handler (" . self::getId() . ") added");
			}
		}
		
		return $result;
	}
	
	public function display(){
		self::displayInfo($this, $this->recordText, $this->recordGets);
	}
	
	private function generateUniqueId(){
		global $db;
		
		// Create the Unique ID (Max 255 chars, Min 25 chars)
		ImportClass("Miscellaneous.Miscellaneous");
		$uniqueId = Miscellaneous::generateRandomString(rand(25, 255));
		
		// Check if the string has already been used
		$count = $db->fetchObject("SELECT id FROM ajaxHandler WHERE id='" . $uniqueId . "'");
		
		if($count == 0){
			// Found a unique id -- store it
			self::setCreateTimestamp(Date::getDbDateTimeFormat());
			
			return $uniqueId;
		} else {
			// Found an id already in the database... try again
			return self::generateUniqueId();
		}
		
		$db->fetchObjectDestroy();
	}
}
?>