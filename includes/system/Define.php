<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

/** 
 * This class handles defines similar to the PHP define() function
 * TODO: Add define scopes
 */ 
class Define {
	private static $definedVars = array();
	
	/** 
	 * Adds a new define
	 */ 
	public static function add($name, $value){
		if(isset($name) && isset($value)){
			// Check if there is data
			if($name !== "" && $value !== ""){
				// Check if it already exists
				if(self::exists($name) == false){
					// Add it
					self::$definedVars[count(self::$definedVars)] = array("name" => $name, "value" => $value);
					
					return true;
				} else {
					Log::warn("Define: add -- define already exists - name = '" . $name . "'");
				}
			} else {
				Log::warn("Define: add -- no value - name = '" . $name . "'    value = '" . $value . "'");
			}
		} else {
			Log::warn("Define: add -- not set - name = '" . $name . "'    value = '" . $value . "'");
		}
		
		return false;
	}
	
	/** 
	 * Checks if a define exists
	 */ 
	public static function exists($name){
		if(isset($name)){
			return (self::search($name) != -1 ? true : false);
		}
		
		return false;
	}
	
	/** 
	 * Returns a define
	 */ 
	public static function get($name){
		if(isset($name)){
			if(($index = self::search($name)) != -1){
				return self::$definedVars[$index]['value'];
			} else {
				Log::warn("Define: get -- defined not found - name = '" . $name . "'");
			}
		} else {
			Log::warn("Define: get -- not set - name = '" . $name . "'");
		}
		
		return false;
	}
	
	/** 
	 * Removes a define
	 */ 
	public static function delete($name){
		if(isset($name)){
			if(($index = self::search($name)) != -1){
				unset(self::$definedVars[$index]);
				return true;
			} else {
				Log::warn("Define: delete -- defined not found - name = '" . $name . "'");
			}
		} else {
			Log::warn("Define: delete -- not sent - name = '" . $name . "'");
		}
		
		return false;
	}
	
	/** 
	 * Searches the array for a given define
	 */ 
	private static function search($name){
		if(isset($name)){
			// Find it in the array
			for($i = 0; $i < count(self::$definedVars); $i++){
				if(self::$definedVars[$i]['name'] === $name){
					return $i;
				}
			}
		}
		
		return -1;
	}
}
?>