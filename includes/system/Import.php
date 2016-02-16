<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

/**
 * Imports files into the code. Similar to the PHP include_once or require_once
 */
// TODO: Add logic for multiple upper characters in file path string... Ex: FolderName
class Importer {
	private static $imported = array();
	
	/**
	 * Adds a new class to the system
	 */
	public static function addClass($class){
		if(isset($class) && strlen($class) > 0){
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
				// Check if file is a system file in libraries/system
				if(self::importFile(BASEPATH.LIBRARY.SYSTEM . "/" . $path) == true){
					// File was in libraries/system
					return true;
				} else {
					// Check if file is a system file in libraries/user
					if(self::importFile(BASEPATH.LIBRARY.USER . "/" . $path) == true){
						// File was in libraries/user
						return true;
					} else {
						// The file doesn't exist as inputted... it could be case sensitive
						$filePath = BASEPATH.INCLUDES;
						foreach($classParts as $part){
							if($directoryHandle = opendir($filePath)){
								// Loop through all of the items
								while(false !== ($directoryEntry = readdir($directoryHandle))){
									if(strtolower($part) === strtolower($directoryEntry)){
										$filePath = $filePath.DS . $directoryEntry;
									}
								}
							} 
						}       

						if(file_exists($filePath.DS . $className . ".php")){
							// Check if file is a system file in libraries/user
							if(self::importFile($filePath.DS . $className . ".php") == true){
								return true;
							} else {
								// File doesn't exist
								Log::fatal("Importer: addClass -- file not found - filename = '" . $path . "' class = '" . $className . "'");
							}
						} else {
							// The file doesn't exist as inputted... it could be case sensitive
							$filePath = BASEPATH.LIBRARY.SYSTEM;
							foreach($classParts as $part){
								if($directoryHandle = opendir($filePath)){
									// Loop through all of the items
									while(false !== ($directoryEntry = readdir($directoryHandle))){
										if(strtolower($part) === strtolower($directoryEntry)){
											$filePath = $filePath.DS . $directoryEntry;
										}
									}
								}
							}

							if(file_exists($filePath.DS . $className . ".php")){
								// Check if file is a system file in libraries/user
								if(self::importFile($filePath.DS . $className . ".php") == true){
									return true;
								} else {
									// File doesn't exist
									Log::fatal("Importer: addClass -- file not found - filename = '" . $path . "' class = '" . $className . "'"); 
								}
							} else {
								// The file doesn't exist as inputted... it could be case sensitive
								$filePath = BASEPATH.LIBRARY.USER;
								foreach($classParts as $part){ 
									if($directoryHandle = opendir($filePath)){
										// Loop through all of the items
										while(false !== ($directoryEntry = readdir($directoryHandle))){
											if(strtolower($part) === strtolower($directoryEntry)){
												$filePath = $filePath.DS . $directoryEntry;
											}       
										}
									}       
								}

								if(file_exists($filePath.DS . $className . ".php")){
									// Check if file is a system file in libraries/user
									if(self::importFile($filePath.DS . $className . ".php") == true){
										return true;
									} else {
										// File doesn't exist
										Log::fatal("Importer: addClass -- file not found - filename = '" . $path . "' class = '" . $className . "'");
									}       
								}
							}
						}
					}
				}
			}
		} else {
			Log::fatal("Importer: addClass -- class name not defined = '" . $class . "'");
		}
		
		return false;
	}
	
	/** 
	 * Adds a new file to the system
	 */
	public static function addFile($name){
		if(isset($name) && strlen($name) > 0){
			// Build path
			if(substr_count($name, "/") > 1){
				$path = $name;
			} else {
				$nameParts = explode(".", $name);
				
				if(substr($name, (strlen($name) - 4), 4) === ".php"){
					array_pop($nameParts);
				}
				
				$fileName = array_pop($nameParts);
				$namePath = strtolower(implode("/", $nameParts));
				
				$path =  $namePath . (substr($namePath, (strlen($namePath) - 1), 1) === "/" ? "" : "/") . $fileName . ".php";
			}
			
			$path = (substr($path, 0, strlen(BASEPATH)) === BASEPATH ? "" : BASEPATH . "/") . $path;
			
			if(self::importFile($path) == true){
				return true;
			} else {
				// File doesn't exist
				Log::fatal("Importer: addFile -- import failed - path = '" . $name . "'");
			}
		} else {
			Log::fatal("Importer: addFile -- name not defined = '" . $name . "'");
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
				}
			} else {
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
	return Importer::addFile($name);
}

?>
