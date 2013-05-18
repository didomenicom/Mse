<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

/**
 * Imports files into the code. Similar to the PHP include_once or require_once
 */
class Importer {
	private static $imported = array();
	
	/**
	 * Adds a new class to the system
	 */
	public static function addClass($class){
		if(isset($class) && substr($class) > 0){
			// Parse it
			$classParts = explode(".", $class);
			$className = ucfirst(array_pop($classParts));
			$classPath = strtolower(implode("/", $classParts));
			$path =  $classPath . "/" . $className . ".php";
			
			// Check if file is in includes
			if(self::importFile(BASEPATH.INCLUDES . "/" . $path) == true){
				// File was in includes
				return true;
			} else {
				Log::info("Importer: add -- file not found in includes - filename = '" . $path . "' class = '" . $className . "'");
				
				// Check if file is a system file in libraries/system
				if(self::importFile(BASEPATH.LIBRARY.SYSTEM . "/" . $path) == true){
					// File was in libraries/system
					return true;
				} else {
					Log::info("Importer: add -- file not found in libraries/system - filename = '" . $path . "' class = '" . $className . "'");
					
					// Check if file is a system file in libraries/user
					if(self::importFile(BASEPATH.LIBRARY.USER . "/" . $path) == true){
						// File was in libraries/user
						return true;
					} else {
						// File doesn't exist
						Log::fatal("Importer: add -- file not found - filename = '" . $path . "' class = '" . $className . "'");
					}
				}
			}
		} else {
			Log::fatal("Importer: add -- class name not defined");
		}
		
		return false;
	}
	
	/** 
	 * Adds a new file to the system
	 */
	public static function addFile($name, $fullPath = false){
		if(isset($name) && $name != ""){
			if($fullPath == false){
				// Build path
				$path = BASEPATH . "/" . implode("/", explode(".", $name)) . ".php";
			} else {
				$path = $name;
			}
			
			if(self::importFile($path) == true){
				return true;
			} else {
				// File doesn't exist
				Log::fatal("Importer: addFile -- import failed - path = '" . $path . "'");
			}
		} else {
			Log::fatal("Importer: addFile -- name not defined");
		}
		
		return false;
	}
	
	/**
	 * Imports a file
	 */
	private static function importFile($path){
		if(isset($path) && $path != ""){
			if(self::searchImported($path) == false){
				if(file_exists($path)){
					// Get the index
					$index = count(self::$imported);
					
					// Add it to the array
					self::$imported[$index] = $path;
					
					// Include the file
					include_once(self::$imported[$index]);
					
					return true;
				} else {
					Log::warn("Importer: importFile -- file doesn't exist - file = '" . $path . "'");
				}
			} else {
				Log::warn("Importer: importFile -- already exists - file = '" . $path . "'");
				return true;
			}
		}
		
		return false;
	}
	
	/** 
	 * Checks if a given name has been previously imported
	 */
	private static function searchImported($name){
		if(isset($name) && $name != ""){
			for($i = 0; $i < count(self::$imported); $i++){
				if(self::$imported[$i] === $name){
					return true;
				}
			}
		}
		
		return false;
	}
}

/** 
 * Import a class 
 */
function ImportClass($name){
	return Importer::addClass($name);
}

/** 
 * Import a file
 */
function ImportFile($name){
	return Importer::addFile($name, true);
}

?>