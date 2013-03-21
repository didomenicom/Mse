<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

class ClassesLibrary {
	/**
	 * Generates a filter string for the classes that display all of the rows
	 * Passes in the filter, the filterArray as the column names in the table, and
	 * filterLogic to determine how to search for the data
	 */
	public static function generateFilterString($inputFilter, $inputFilterArray, $filterLogic = array()){
		if(isset($inputFilter) && isset($inputFilterArray) && is_array($inputFilter) == true && is_array($inputFilterArray) == true){
			$outputString = "";
			$itemsExist = false;
			
			foreach($inputFilter as $filter => $filterValue){
				if(isset($filterValue) && ($filterValue == false || strlen($filterValue) > 0)){
					$index = array_search($filter, $inputFilterArray);
					
					if($index !== false){
						// There is 1 to handle
						$outputString .= ($itemsExist == true ? " &&" : "");
						
						$itemsExist = true;
						
						$outputString .= " " . $inputFilterArray[$index] . (isset($filterLogic[$inputFilterArray[$index]]) ? ($filterLogic[$inputFilterArray[$index]] == true ? " = " : " != ") : " = ") . "'" . ($filterValue == false ? 0 : $filterValue) . "'";
					} elseif(array_key_exists($filter, $inputFilterArray) == true){
						// There is one, but the database value is different than the key index
						$outputString .= ($itemsExist == true ? " &&" : "");
						
						$itemsExist = true;
						
						$outputString .= " " . $inputFilterArray[$filter] . "='" . ($filterValue == false ? 0 : $filterValue) . "'";
					}
				}
			}
			
			// Cheick if there are items -- if so add the WHERE clause
			$outputString = ($itemsExist == true ? " WHERE " . $outputString : $outputString);
			
			return $outputString;
		}
	}
	
	public static function generateSortingString(){
		
	}
	
	public static function generateRowsCountString(){
		
	}
}
?>