<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2013 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

/**
 * This class handles all of the API requests
 * TODO: Document login id is based on $_GET['token']
 */
class API {
	/**
	 * This function handles the API request
	 */
	public static function handler(){
		ImportClass("PhpasswordPusher.PHPasswordPusher");
		// Check for the request id
		if(isset($_POST["id"]) && $_POST["id"] != NULL){ // TODO: Switch to post class
			// The id is encrypted... unencrypt it 
			// TODO: Swith to PHPasswordPusher
			if(PHPasswordPusher::stringInCorrectFormat($_POST["id"]) == true){
				$handlerId = NULL;
				if(PHPasswordPusher::retrieveCredential($_POST["id"], $handlerId) == true){
					return self::processHandler($handlerId);
				}
			} else {
				$handlerId = Encryption::decrypt($_POST["id"]);
				
				return self::processHandler($handlerId);
			}
		} else {
			// This is considered a request for a list of available APIs
			return self::listAvailableHandlers();
		}
		
		return false;
	}
	
	/**
	 * This function takes the id passed in (handlerId), decrypts it, and checks if the entry exists in the database
	 * If the entry exists in the db it will set $recordId to the ID of the entry and return true
	 * Otherwise, it returns false (the value of $recordId should be 0).
	 * If this function returns false, $recordId should be ignored
	 */
	private static function validateHandlerId($handlerId, &$recordId = 0){
		global $db;
		
		if(isset($handlerId)){
			// Check if handler id the exists
			$parts = $db->fetchAssoc("SELECT id FROM apiHandler WHERE handlerId='" . $handlerId . "'");
			
			if(isset($parts->id) && $parts->id > 0){
				$recordId = $parts->id;
				
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * 
	 */
	private static function processHandler($handlerId){
		if(self::validateHandlerId($handlerId, $recordId) == true){
			if(is_numeric($recordId) && $recordId > 0){
				// Valid id
				ImportClass("ApiHandler.ApiHandler");
				
				$apiHandler = new ApiHandler($recordId);
				
				// Find the path to the class
				// The Class Name is the path & Caller Function is the function name
				$functionName = $apiHandler->getCallerFunction();
				$path = $apiHandler->getClassName();
				
				// Check to make sure there is no spaces, tabs, line breaks, etc (this shouldn't happen to begin with)
				// TODO: Add code so we don't hit this
				if(substr_count($path, " ") > 0){
					// Just die now... we won't succeed in loading the file... 
					return false;
				}
				
				// Break it apart
				$parts = explode("/", $path);
				$fileName = array_pop($parts);
				
				if(count($parts) > 0){
					$filePath;
					if(strtolower($parts[0]) === "user"){
						$filePath = BASEPATH.LIBRARY.USER . "/api";
					}
					
					if(strtolower($parts[0]) === "system"){
						$filePath = BASEPATH.LIBRARY.SYSTEM . "/api";
					}
					
					foreach($parts as $part){
						if($directoryHandle = opendir($filePath)){
							// Loop through all of the items
							while(false !== ($directoryEntry = readdir($directoryHandle))){
								if(strtolower($part) === strtolower($directoryEntry)){
									$filePath = $filePath.DS . $directoryEntry;
								}
							}
						}
					}
					
					if(ImportFile($filePath.DS . $fileName . ".php") == true){
						// Found and grabbed the file
						// Execute it
						return $functionName($_POST["task"]);
					}
				}
			}
		}
	}
	
	/**
	 * Lists all of the available handlers that can be called
	 * TODO: Use XMLHandler class
	 */
	private static function listAvailableHandlers(){
		ImportClass("ApiHandler.ApiHandlers");
		$outputXML = "<apiHandlers>";
		
		// Check if the user is logged in 
		$allowUserLogin = new ConfigOption(array("component" => "api", "name" => "allowUserLogin"));
		$allowUserLogin = $allowUserLogin->getValue();
		
		$userLoggedIn = false;
		if(isset($_GET['token'])){
			ImportClass("User.UserSession");
				
			$sessionId = Encryption::decrypt(Url::base64_decode($_GET['token']));
			
			// Create the UserSession
			$userSession = new UserSession($sessionId);
			
			if($userSession->getUserId() > 0){
				$userLoggedIn = true;
			}
		}
		
		// If the user is not logged in then display the login handler
		if($userLoggedIn == false && $allowUserLogin == 1){ // TODO: Check allowUserLogin against true
			ImportClass("PhpasswordPusher.PHPasswordPusher");
			
			$userLoginHandlerId = new ConfigOption(array("component" => "api", "name" => "userLoginHandlerId"));
			$uniqueId = PHPasswordPusher::createCredential($userLoginHandlerId->getValue());
			
			// Send out an email
			if($uniqueId != NULL){
				$outputXML .= "<handler>";
				$outputXML .= "<id>" . $uniqueId . "</id>";
				$outputXML .= "<name>Login</name>";
				$outputXML .= "</handler>";
			}
		}
		
		// Loop through the handlers
		$hanlders = new ApiHandlers();
		
		if($hanlders->rowsExist()){
			while($hanlders->hasNext()){
				$handler = $hanlders->getNext();
				
				if((($userLoggedIn == false && $handler->getGuestAccess() == 1) || $userLoggedIn == true) && $handler->getRestricted() == false){
					$outputXML .= "<handler>";
					$outputXML .= "<id>" . $handler->getHandlerId() . "</id>";
					$outputXML .= "<name>" . $handler->getName() . "</name>";
					$outputXML .= "</handler>";
				}
			}
		}
		
		$outputXML .= "</apiHandlers>";
		
		echo $outputXML;
		
		return true;
	}
}
?>
