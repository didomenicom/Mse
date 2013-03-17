<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

class Library {
	/**
	 * Converts a true/false number into Yes/No text
	 */
	public function getYesNoText($inputNumber){
		switch($inputNumber){
			case 0:
				return "No";
				break;
			case 1:
				return "Yes";
				break;
			default:
				Log::warn("Library: getYesNoText -- Unknown value - " . $inputNumber);
				return "Unknown";
				break;
		}
	}
	
	/**
	 * Converts a true/false number into True/False text
	 */
	public function getTrueFalseText($inputNumber){
		switch($inputNumber){
			case 0:
				return "False";
				break;
			case 1:
				return "True";
				break;
			default:
				Log::warn("Library: getTrueFalseText -- Unknown value - " . $inputNumber);
				return "Unknown";
				break;
		}
	}
	
	/**
	 * Takes a number and formats into a USD currency format
	 */
	public function formatCurrencyNumber($inputValue, $displayUSDSymbol = true){
		if(isset($inputValue)){
			return ($displayUSDSymbol == true ? "$" : "") . number_format($inputValue, 2, '.', ',');
		}
	}
	
	/**
	 * Checks a class (usually before a save) to see if there are any changes. 
	 * Any changes found will be put into a string and returned. 
	 * It will write the changed text to the passed in variable
	 * Returns true if the compare was successful, false otherwise
	 */
	public function determineClassChanges($classObject, &$outputText, $variableText = NULL){
		// Create ReflectionProperty to determine if there is access to the recordInfo variable
		$classRecordInfoAccess = new ReflectionProperty($classObject, "recordInfo");
		
		// Check if we have access to the record info
		if($classRecordInfoAccess->isProtected() == true){
			if($classObject->recordInfo['id'] > 0){
				// Create an object to compare against
				$className = $classRecordInfoAccess->getDeclaringClass()->getName();
				$compareObject = new $className($classObject->recordInfo['id']);
				
				// Check for access to compare
				$compareRecordInfoAccess = new ReflectionProperty($compareObject, "recordInfo");
				
				if($compareRecordInfoAccess->isProtected() == true){
					// All good start comparing
					$outputText = "";
					$class = $classObject->recordInfo;
					$compare = $compareObject->recordInfo;
					
					// Loop through all of the items in class and see if one exists in compare
					// If one exists, check for changes. If one doesn't exist, report add
					foreach($class as $classKey => $classValue){
						if(isset($compare[$classKey]) == true){
							// Exists check for changes
							if($classValue !== $compare[$classKey]){
								// There is a change
								// Check if there is variable text
								$outputText .= (isset($variableText) && isset($variableText[$classKey]) ? $variableText[$classKey] : $classKey) . " from " . $compare[$classKey] . " to " . $classValue . "<br />";
							}
						} else {
							// Doesn't exist -- this is an add
							$outputText .= (isset($variableText) && isset($variableText[$classKey]) ? $variableText[$classKey] : $classKey) . " added value " . $classValue . "<br />";
						}
					}
					
					// Check for trailing br and remove it
					if(substr($outputText, (strlen($outputText) - 6), 6) === "<br />"){
						$outputText = substr($outputText, 0, (strlen($outputText) - 6));
					}
					
					return true;
				} else {
					// For some reason there is no access
					Log::warn("Library: determineClassChanges -- Do not have protected access to the comparing object");
				}
			} else {
				// There was nothing to compare -- user doesn't exist
				Log::warn("Library: determineClassChanges -- There was nothing to compare");
			}
		} else {
			// Alert that this function cannot dertermine the changes -- no access
			Log::warn("Library: determineClassChanges -- Do not have protected access to the object");
		}
		
		return false;
	}

	/**
	 * Displays all of the information from a class
	 * Assumptions:
	 * Method(1) is display text
	 * Method is in the format getXXX()
	 */
	public function displayInfo($classObject, $variableText, $classGets = NULL){
		$callableFunction = new ReflectionClass($classObject);
		
		$methods = $callableFunction->getMethods(ReflectionMethod::IS_PUBLIC);
		$calls = array();
		
		foreach($methods as $method){
			if(preg_match("(get)", $method) == true && $method->class !== "Library"){
				$name = $method->name;
				array_push($calls, $name);
			}
		}
		
		// Loop through all of the variableText items
		foreach($variableText as $variable => $value){
			if(isset($classGets[$variable])){
				$indx = array_search($classGets[$variable], $calls);
				if($indx != false){
					// This is the function
					echo "<strong>" . $value . ":</strong> " . $classObject->$calls[$indx](1) . "<br />";
				}
			}
		}
		
		return true;
	}
}
?>