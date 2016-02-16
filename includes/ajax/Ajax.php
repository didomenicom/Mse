<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

/**
 * This class handles all of the ajax requests
 */
class Ajax {
	
	/**
	 * 
	 */
	public function Ajax(){
		// Nothing to configure
	}
	
	/**
	 * 
	 */
	public function handler(){
		// Check for the request id
		if(isset($_POST["id"]) && isset($_POST["task"]) && $_POST["id"] != NULL && $_POST["task"] != NULL){ // TODO: Switch to post class
			// The id is encrypted... unencrypt it 
			// TODO: Swith to PHPasswordPusher
			$handlerId = Encryption::decrypt($_POST["id"]);
			
			if(self::validHandlerId($handlerId, $ajaxId) == true){
				if($ajaxId > 0){
					// Valid id
					ImportClass("AjaxHandler.AjaxHandler");
					
					$ajaxHandler = new AjaxHandler($ajaxId);
					
					// Find the path to the class
					// The Class Name is the path & Caller Functino is the function name
					$functionName = $ajaxHandler->getCallerFunction();
					$path = $ajaxHandler->getClassName();
					
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
							$filePath = BASEPATH.LIBRARY.USER . "/ajax";
						}

						if(strtolower($parts[0]) === "system"){
							$filePath = BASEPATH.LIBRARY.SYSTEM . "/ajax";
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
		
		return false;
	}
	
	private function validHandlerId($handlerId, &$rowId = 0){
		global $db;
		
		if(isset($handlerId)){
			// Check if handler id the exists
			$parts = $db->fetchAssoc("SELECT id FROM ajaxHandler WHERE handlerId='" . $handlerId . "'");
			
			if(isset($parts->id) && $parts->id > 0){
				$rowId = $parts->id;
				
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * 
	 */
	public static function generateHandlerId($inputHandlerName){
		global $db; 
		
		if($inputHandlerName != null && strlen($inputHandlerName) > 0){
			$rowsCount = $db->fetchObject("SELECT handlerId FROM ajaxHandler WHERE name='" . $inputHandlerName . "'");
			
			if($rowsCount > 0){
				if($rowsCount > 1){
					// This is a problem...
					Log::error("Ajax.generateHandlerId('" . $inputHandlerName . "') count is greater than 1");
				}
				
				while($db->fetchObjectHasNext() == true){
					$row = $db->fetchObjectGetNext();
					
					if(isset($row->handlerId)){
						return Encryption::encrypt($row->handlerId);
					}
				}
				$db->fetchObjectDestroy();
			}
		}
		
		return "";
	}
}
?>
