<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
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
					
					// Break it apart
					$parts = explode("/", $path);
					
					// Check if it is in valid format
					if(count($parts) > 0){
						$filename = array_pop($parts);
						
						// Check if the first part is "system"... system directory compared to the user directory
						$filePath = (strtolower(array_shift($parts)) === "system" ? "system".DS : "user".DS) . "ajax".DS;
						
						foreach($parts as $part){
							$filePath .= strtolower($part).DS;
						}
						
						// Build path 
						if(ImportFile(BASEPATH.DS.LIBRARY.DS . $filePath . $filename . ".php") == true){
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
			
			if($parts->id > 0){
				$rowId = $parts->id;
				
				return true;
			}
		}
		
		return false;
	}
}
?>