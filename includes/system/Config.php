<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

/** 
 * This class handles the config file without giving direct access to it
 */ 
class Config {
	private $configRead = false;
	private $configArray = array();
	
	/** 
	 * Constructor
	 * Reads the config file in and parses it. 
	 * TODO: Change parse to handle values not inside " and "
	 */ 
	public function Config(){
		// Read in config file
		$fileContents = File::readFile(BASEPATH . "/config.php");
		
		// Parse it into array
		// Format: public $varName = $varValue
		foreach($fileContents as $row){
			// Regex for format
			if(preg_match('/(public \$).+/', $row) != false){
				// Found a config option, break it apart
				$name = "";
				preg_match('/(\$)\w+/', $row, $name);
				$name = $name[0];
				
				if($name != ""){
					// Rip of the $
					$name = (substr($name, 0, 1) === "$" ? substr($name, 1, strlen($name)) : $name);
					
					$value = "";
					preg_match('/(\").+(\")/', $row, $value);
					
					if(count($value) > 1){
						$value = $value[0];
					} elseif(is_array($value)){
						$value = "";
					}
					
					if($value != ""){
						// Rip of the "
						$value = (substr($value, 0, 1) === '"' ? substr($value, 1, strlen($value)) : $value);
						$value = (substr($value, (strlen($value) - 1), strlen($value)) === '"' ? substr($value, 0, (strlen($value) - 1)) : $value);
					}
					
					// Add to array
					$this->configArray[$name] = $value;
				}
			}
		}
		
		$this->configRead = true;
	}
	
	/** 
	 * Gets the value of a variable name
	 * Returns the value or false
	 * TODO: Change return to NULL and report error when unknown name
	 */ 
	public function getVar($name){
		if(isset($name) && $name !== ""){
			if($this->configRead == true){
				if(array_key_exists($name, $this->configArray)){
					return $this->configArray[$name];
				}
			}
		}
		
		return false;
	}
	
	/** 
	 * Sets the value for a variable name
	 * Returns true if write is successful, false otherwise
	 */ 
	public function setVar($name, $value){
		if(isset($name) && $name !== ""){
			if($this->configRead == true){
				if(array_key_exists($name, $this->configArray)){
					$this->configArray[$name] = $value;
					return true;
				}
			}
		}
		
		return false;
	}
	
	/** 
	 * This function will take all of the changes made to the config file and 
	 * write it back once execution completes. 
	 * TODO: See if the destruct method can be defined in the constructor
	 * TODO: Set flag when a write is done and dont write back if not set
	 */ 
	public function __destruct(){
		// Save everything back to file
		// TODO: Save
//		print "Destroying \n";
   }
}

?>