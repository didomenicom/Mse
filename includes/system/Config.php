<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

/** 
 * This class handles the config file without giving direct access to it
 */ 
class Config {
	private $configRead = false;
	private $configWrite = false;
	private $configArray = array();
	
	/** 
	 * Constructor
	 * Reads the config file in and parses it. 
	 * TODO: Change parse to handle values not inside " and "
	 */ 
	public function Config(){
		// Read in config file
		$fileContents = File::read(BASEPATH . "/config.php");
		
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
	 * Gets the value of a variable name from the config file
	 * Returns the value or false
	 */ 
	public function getSystemVar($name){
		if(isset($name) && $name !== ""){
			if($this->configRead == true){
				if(array_key_exists($name, $this->configArray)){
					return $this->configArray[$name];
				}
			}
		}
		
		return NULL;
	}
	
	/** 
	 * Sets the value for a variable name from the config file
	 * Returns true if write is successful, false otherwise
	 */ 
	public function setSystemVar($name, $value){
		if(isset($name) && $name !== ""){
			if($this->configRead == true){
				if(array_key_exists($name, $this->configArray)){
					$this->configArray[$name] = $value;
					$this->configWrite = true;
					return true;
				}
			}
		}
		
		return false;
	}
	
	
	/** 
	 * Gets the value of a variable name from the database
	 * Returns the value or false
	 * TODO: Add ConfigOption Class?
	 */ 
	public function getVar($component, $name){
		global $db;
		
		if(isset($component) && isset($name) && $component !== "" && $name !== ""){
			// Search for the name and $component
			$record = $db->fetchAssoc("SELECT * FROM config WHERE component='" . $component . "' AND name='" . $name . "'");
			
			if(isset($record->id)){
				// Found it
				return $record->value;
			}
		}
		
		return NULL;
	}
	
	/** 
	 * Sets the value for a variable name from the database
	 * Returns true if write is successful, false otherwise
	 */ 
	public function setVar($component, $name, $value){
		global $db;
		
		if(isset($name) && $name !== ""){
			if(self::varExists($component, $name) == true){
				// The record exists
				$result = $db->update("UPDATE config SET " .
						"value='" . addslashes(self::getValue()) . "' " .
						"WHERE component='" . $component . "' AND name='" . $name . "'");
				
				return $result;
			}
		}
		
		return false;
	}
	
	/** 
	 * Create a new record in the database
	 * Returns true if write is successful, false otherwise
	 * TODO: Check if the component name is valid
	 */ 
	public function addVar($component, $type, $name, $value){
		global $db;
		
		if(isset($component) && isset($type) && isset($name) && $component !== "" && $type !== "" && $name !== ""){
			if(self::varExists($component, $name) == false){
				// Nothing in the DB exists
				$result = $db->insert("INSERT INTO config (component, type, name, value) VALUES (" .
						"'" . addslashes($component) . "', " .
						"'" . addslashes($type) . "', " .
						"'" . addslashes($name) . "', " .
						"'" . addslashes($value) . "')");
				
				return $result;
			}
		}
		
		return false;
	}
	
	/**
	 * Checks if a key exists in the config database
	 * Returns true if it exists, false otherwise
	 */
	private function varExists($component, $name){
		global $db;
		if(isset($component) && isset($name) && $component !== "" && $name !== ""){
			// Search for the name and key
			$record = $db->fetchAssoc("SELECT * FROM config WHERE component='" . $component . "' AND name='" . $name . "'");
			
			if(isset($record->id)){
				// Found it
				return true;
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
		if($this->configWrite == true){
			// Writeback required
		}
	}
	
	/**
	 * Returns the value of the ConfigOption or null on failure
	 */
	public static function getValue($inputComponent, $inputName){
		if(isset($inputComponent) && isset($inputName)){
			$configOption = new ConfigOption(array("component" => $inputComponent, "name" => $inputName));
			
			return $configOption->getValue();
		}
		
		return null;
	}
	
	/**
	 * True on successful update, false otherwise
	 */
	public static function setValue($inputComponent, $inputName, $inputValue){
		if(isset($inputComponent) && isset($inputName)){
			$configOption = new ConfigOption(array("component" => $inputComponent, "name" => $inputName));
			
			$configOption->setValue($inputValue);
			
			if($configOption->save() == true){
				return true;
			}
		}
		
		return false;
	}
}

?>
