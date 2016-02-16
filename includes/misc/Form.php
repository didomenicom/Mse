<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

class Form {
	private static $formParts = array();
	
	/**
	 * This function grabs all of the form stuff ($_POST)
	 * If text is passed in, it returns the text if it exists, NULL otherwise
	 * If no text is passed in, it returns the entire array, no matter how small
	 * TODO: Describe what text does better
	 */
	public static function getParts($notSanitize = NULL, $text = NULL){
		if(count(self::$formParts) == 0){
			// Get all of the parts of the url
			foreach($_POST as $key => $value){
				// Cleanup the parts
				if(strlen($key) > 0){
					$key = strip_tags($key);
				}
				
				if(is_array($value)){
					// TODO: Handle multiple levels of arrays (recursion) 
				} elseif(strlen($value) > 0){
					if(!is_null($notSanitize)){
						if(!in_array($key, $notSanitize)){
							$value = strip_tags($value);
						}
					} else {
						$value = strip_tags($value);
					}
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
