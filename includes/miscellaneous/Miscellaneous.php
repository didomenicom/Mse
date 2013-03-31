<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

class Miscellaneous {
	/** 
	 * Generates a random string (a-zA-Z0-9)
	 * TODO: Add special characters
	 */
	public static function generateRandomString($inputLength){
		if($inputLength > 0){
			$outputString = "";
			
			for($i = 0; $i < $inputLength; $i++){
				// Generate a random number for an ascii character
				// 0-9 == 48-57
				// A-Z == 65-90
				// a-z == 97-122
				$num = rand(48, 108);
				
				// Do a bit of math to get rid of special chars
				if($num > 57){
					$num += 7;
				}
				
				if($num > 90){
					$num += 7;
				}
				
				// Convert to ascii char and add to string
				$outputString .= chr($num);
			}
			
			return $outputString;
		}
	}
}

?>