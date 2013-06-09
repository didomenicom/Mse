<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

class Form {
	private static $formParts = array();
	
	/**
	 * This function grabs all of the form stuff ($_POST)
	 * If text is passed in, it returns the text if it exists, NULL otherwise
	 * If no text is passed in, it returns the entire array, no matter how small
	 */
	public static function getParts($text = NULL){
		if(count(self::$formParts) == 0){
			// Get all of the parts of the url
			foreach($_POST as $key => $value){
				// Cleanup the parts
				if(strlen($key) > 0){
					$key = strip_tags($key);
				}
				
				if(strlen($value) > 0){
					$value = strip_tags($value);
				}
				
				self::$formParts[$key] = $value;
			}
		}
		
		if($text != NULL && $text != ""){
			if(isset(self::$formParts[$text])){
				return self::$formParts[$text];
			} else {
				return NULL;
			}
		} else {
			return self::$formParts;
		}
	}
}
?>