<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

/**
 * Handles the user session
 * 
 * TODO: Install script creates DB jobs to remove expired entries (based on expiredtimestamp)
 * 		 Install script creates DB job to remove reserved entries 10 minutes after created and not used
 * 		 Remove reserved field and use removetimestamp == 0 instead
 */

class UserSession {
	private $sessionId = NULL;
	private $recordInfo = array();
	private $duration = false;
	
	/**
	 * Create a new user session
	 * If a session id is passed it tries to find it, 
	 * if not it will generate a new one
	 */
	public function UserSession($sessionId = NULL){
		if(isset($sessionId)){
			// A session might exist, check the db
			$this->sessionId = Encryption::decrypt($sessionId);
			self::buildData();
		} else {
			// Session doesn't exist, create a new one
			if(self::generateSessionId() == false){
				// Something happened, report it
				// TODO: Report
			}
		}
	}
	
	/** 
	 * Returns an encrypted session id
	 * Note: Session ID cannot be seen outside of this class
	 */ 
	public function getSessionId(){
		if(isset($this->sessionId)){
			return Encryption::encrypt($this->sessionId);
		} else {
			return NULL;
		}
	}
	
	/** 
	 * Sets an unencrypted id
	 */ 
	public function setSessionId($inputSessionId){
		if(isset($inputSessionId)){
			$this->sessionId = Encryption::decrypt($inputSessionId);
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Fills in the class from the db
	 */
	private function buildData(){
		global $db;
		if(isset($this->sessionId)){
			// Search the db for the user
			
			$info = $db->fetchAssoc("SELECT * FROM sessions WHERE id='" . $this->sessionId . "'");
			
			$this->recordInfo['userId'] = $info->userId;
			$this->recordInfo['createTimeStamp'] = $info->createTimeStamp;
			$this->recordInfo['removeTimeStamp'] = $info->removeTimeStamp;
			$this->recordInfo['ipAddress'] = $info->ipAddress;
			$this->recordInfo['browser'] = $info->browser;
			$this->recordInfo['reserved'] = $info->reserved;
		}
	}
	
	public function getUserId(){
		return $this->recordInfo['userId'];
	}
	
	public function setUserId($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['userId'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	public function getCreateTimeStamp(){
		if(isset($this->recordInfo['createTimeStamp']) && $this->recordInfo['createTimeStamp'] !== ""){
			return $this->recordInfo['createTimeStamp'];
		} else {
			return Date::getDbDateTimeFormat();
		}
	}
	
	public function setCreateTimeStamp($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['createTimeStamp'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	public function getRemoveTimeStamp(){
		global $Config;
		
		if(isset($this->recordInfo['removeTimeStamp']) && $this->recordInfo['removeTimeStamp'] !== ""){
			return $this->recordInfo['removeTimeStamp'];
		} else {
			// Default case
			if($this->duration == false){
				// Short
				$timestamp = Date::getCurrentTimeStamp();
				$timestamp += ($Config->getSystemVar('sessionDuriation') * 60); // sessionDuriation is in minutes
				
				return Date::getDbDateTimeFormat($timestamp);
			} else {
				// Long (default 1 year)
				$timestamp = Date::getCurrentTimeStamp();
				// TODO: Handle leap year
				$timestamp += 31536000; // 365 days
				
				
				return Date::getDbDateTimeFormat($timestamp);
			}
		}
	}
	
	public function setRemoveTimeStamp($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['removeTimeStamp'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	public function getIpAddress(){
		if(isset($this->recordInfo['ipAddress']) && $this->recordInfo['ipAddress'] !== ""){
			return $this->recordInfo['ipAddress'];
		} else {
			// Default case
			return Server::get('REMOTE_ADDR');
		}
	}
	
	public function setIpAddress($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['ipAddress'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	public function getBrowser(){
		if(isset($this->recordInfo['browser']) && $this->recordInfo['browser'] !== ""){
			return $this->recordInfo['browser'];
		} else {
			// Default case
			return Server::get('HTTP_USER_AGENT');
		}
	}
	
	public function setBrowser($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['browser'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	public function getReserved(){
		return $this->recordInfo['reserved'];
	}
	
	public function setReserved($inputValue){
		if(isset($inputValue)){
			$this->recordInfo['reserved'] = $inputValue;
			return true;
		}
		
		return false;
	}
	
	/**
	 * This function sets the duration of removetimestamp
	 */
	public function setDuriation($input){
		if(isset($input)){
			if($input == true){
				$this->duration = true;
			}
		}
	}
	
	/**
	 * This function saves the data in the db.
	 * Note there is no insert function because that is handled in 
	 * generateSessionId
	 */
	public function save(){
		global $db;
		
		if(isset($this->sessionId)){
			// Session exists
			$result = $db->update("UPDATE sessions SET " . 
				"userId='" . self::getUserId() . "', " . 
				"removeTimeStamp='" . self::getRemoveTimeStamp() . "', " . 
				"ipAddress='" . self::getIpAddress() . "', " . 
				"browser='" . self::getBrowser() . "', " . 
				"reserved='" . self::getReserved() . "' " . 
				"WHERE id='" . $this->sessionId . "'");
			
			if($result == true){
				// Success
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * This function deletes the data in the db.
	 */
	public function delete(){
		global $db;
		
		if(isset($this->sessionId)){
			// Session exists
			$result = $db->delete("DELETE FROM sessions WHERE id='" . $this->sessionId . "'");
			
			if($result == true){
				// Success
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Sets the information needed to reference the logged in user
	 */
	public function register($userId, $duriation){
		if(isset($userId)){
			self::setUserId($userId);
			self::setDuriation($duriation);
			self::setReserved(false);
			return true;
		}
		
		return false;
	}
	
	/**
	 * Destroys the session so the user is logged out
	 */
	public function destroy(){
		return self::delete();
	}
	
	/** 
	 * Generates a unique id and reserves it in the database.
	 * Generated id is stored in class variable. 
	 * Note: Session ID can only be generated once. If a new 
	 * Session ID is needed, create a new class
	 * 
	 * Returns true on success or false
	 */
	private function generateSessionId(){
		global $db;
		
		if(!isset($this->sessionId)){
			// One has not been created yet
			
			// Create the Unique ID (Max 255 chars, Min 25 chars)
			ImportClass("Miscellaneous.Miscellaneous");
			$uniqueId = Miscellaneous::generateRandomString(rand(25, 255));
			
			// Check if the string has already been used
			$count = $db->fetchObject("SELECT id FROM sessions WHERE id='" . $uniqueId . "'");
			
			if($count == 0){
				// Found a unique id -- store it
				self::setCreateTimestamp(Date::getDbDateTimeFormat());
				self::setReserved(true);
				$result = $db->insert("INSERT INTO sessions (id, createTimeStamp, reserved) VALUES ('" . $uniqueId . "', '" . self::getCreateTimeStamp() . "', '" . self::getReserved() . "')");
				
				if($result == true){
					// Added, store it
					$this->sessionId = $uniqueId;
					
					// Success
					return true;
				}
			} else {
				// Found an id already in the database... try again
				return self::generateSessionId();
			}
			
			$db->fetchObjectDestroy();
		}
		
		return false;
	}
}

?>