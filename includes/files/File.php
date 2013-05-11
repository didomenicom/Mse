<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

class File {
	
	public function File(){
		// Constructor
	}
	
	public function save($path, $contents){
		// Writes contents to disk
	}
	
	// Removes file
	public function delete($path){
		if(file_exists($path)){
			unset($path);
			
			return 1;
		}
		
		return -1;
	}
	
	public function write($path, $contents){
		// Write contents to disk
		// TODO: 
	}
	
	public static function exists($path, $filename){
		// Checks if file exists
		if(isset($filename) && isset($path)){
			if(file_exists($path . "/" . $filename)){
				return true;
			}
		}
		
		return false;
	}
	
	public function getFilename($path){
		// Returns filename from path (with extension)
	}
	
	public function getFileLocation($path){
		// Searches for file in given path
	}
	
	public function read($path){
		// Reads a file into array (by line)
	}
	
	public function copy($origPath, $newPath){
		// Copies a file
	}
	
	public function move($origPath, $newPath){
		// Moves a file
	}
	
	public function open($path){
		// Opens a file handler
		// Check if file exists
		if(File::exists($path)){
			
		}
		
		return -1;
	}
	
	public function close($fileHandler){
		// Closes a file handler
	}
	
	public function cleanupFilename($filename){
		// Makes filename valid
	}
	
	public function getFileExtension($path, $checkExtension = false){
		// Returns file extension
	}
	
	public function validFileExtension(){
		// Include Libraries/FileExtensions
		Import("Library.File.FileExtensions"); // TODO: Fix
		
		// Get extensions
		$array = FileExtensions::getExtensions();
		
		// TODO: Finish
	}
	
	public function createFile($path, $contents){
		// Builds file like config.php
	}
	
	public static function directoryContents($path){
		// Puts all of the filenames of a directory in an array
		// Check if path exists as a dir
		if(is_dir($path)){
			$output = array();
			
			// Is a directory read it
			$dir = opendir($path);
			
			// Loop through dir
			while(($file = readdir($dir)) !== false){
				if($file !== "." && $file !== ".."){
					array_push($output, $file);
				}
			}
			
			closedir($dir);
			
			return $output;
		} else {
			Log::fatal("File: directoryContents -- path is not a directory - path = '" . $path . "'");
			return array();
		}
	}
	
	public static function readFile($fileName){
		if(isset($fileName) && $fileName != ""){
			// Check if file exists
			if(file_exists($fileName)){
				// Open file
				$filePointer = fopen($fileName, "r");
				
				if($filePointer != FALSE){
					// Finally, read it into array
					$outputArray = array();
					while(!feof($filePointer)){
						array_push($outputArray, fgets($filePointer));
					}
					
					fclose($filePointer);
					
					return $outputArray;
				} else {
					Log::fatal("File: readFile -- unable to read file - name = '" . $fileName . "'");
				}
			} else {
				Log::fatal("File: readFile -- file doesn't exist - name = '" . $fileName . "'");
			}
		} else {
			Log::info("File: readFile -- filename empty");
		}
		
		return NULL;
	}
}

?>