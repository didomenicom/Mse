<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
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
	 * Finds the user and returns the users name
	 * This is used to get the user name of users that are not the currently logged in user
	 */
	public function getUsernameText($inputUserId){
		if($inputUserId > 0){
			ImportClass("User.User");
			$user = new User($inputUserId);
			return $user->getName();
		} else if($inputUserId == -1){
			return "System";
		} else {
			return "N/A";
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
	 * TODO: Change Class Object to array with variableText and class gets inside
	 * TODO: Add logic for when to display
	 * TODO: Add logic to handle calls... Ex: functionName(3)
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
				if(($indx = array_search($classGets[$variable], $calls)) !== false){
					// This is the function
					echo "<strong>" . $value . ":</strong> " . $classObject->$calls[$indx](1) . "<br />";
				}
			}
		}
		
		return true;
	}
	
	/**
	 * This function will trim the end of a string if inputTrimCharacters is at the end of the string
	 * TODO: Have it work anywhere in the string (remove EndOf off the name)
	 * TODO: Use regex to do this
	 */
	public static function trimEndOfString($inputString, $inputTrimCharacters){
		if(isset($inputString) && isset($inputTrimCharacters)){
			if(substr($inputString, (strlen($inputString) - strlen($inputTrimCharacters)), strlen($inputTrimCharacters)) === $inputTrimCharacters){
				return substr($inputString, 0, (strlen($inputString) - strlen($inputTrimCharacters)));
			}
		}
		
		return $inputString;
	}
	
	/**
	 * This function will trim the beginning of a string if inputTrimCharacters is at the beginning of the string
	 * TODO: Have it work anywhere in the string (remove BeginningOf off the name)
	 * TODO: Use regex to do this
	 */
	public static function trimBeginningOfString($inputString, $inputTrimCharacters){
		if(isset($inputString) && isset($inputTrimCharacters)){
			if(substr($inputString, 0, strlen($inputTrimCharacters)) === $inputTrimCharacters){
				return substr($inputString, strlen($inputTrimCharacters), strlen($inputString));
			}
		}
		
		return $inputString;
	}
	
	/**
	 * This function... 
	 * inputXMLString is the string in the components table that has the data based upon the dynamic field structure
	 * The dynamic field structure is build out of the inputXMLStructureString and inputXMLHeaderString where the 
	 * inputXMLHeaderString is display info for inputXMLStructureString
	 * 
	 * TODO: Handle the filter
	 */
	public function getDynamicFieldsArray($inputXMLString, $inputXMLStructureString, $filter = NULL){
		ImportClass("Xml.XMLHandler");
		
		if(is_string($inputXMLStructureString) && strlen($inputXMLStructureString) > 0){
			// Get the config structure
			$xmlHandler = new XMLHandler();
			$xmlHandler->setXMLString(trim(preg_replace('/[\t\n\r]+/', '', $inputXMLStructureString)));
			$structureXMLArray = $xmlHandler->getNodeArray("config > configRecord");
			
			// Build the array strcture
			$dynamicFieldStructureArray = array();
			
			foreach($structureXMLArray as $struct){
				$tmpArray = array();
				$struct = $struct['configValue'];
				
				foreach($struct as $items){
					$tmpArray[$items['index']] = $items['value'];
					
					if(isset($items['options'])){
						$tmpArray['options'] = array();
						foreach($items['options'][0]['option'] as $option){
							$tmpArray['options'][$option['index']] = $option['displayName'];
						}
					}
				}
				
				if(isset($tmpArray['index'])){
					$dynamicFieldStructureArray[$tmpArray['index']] = $tmpArray;
				}
			}
			
			// Break apart the input string
			if(isset($inputXMLString) && strlen($inputXMLString) > 0){
				$xmlHandler = new XMLHandler();
				$xmlHandler->setXMLString(trim(preg_replace('/[\t\n\r]+/', '', $inputXMLString)));
				$dynamicXMLArray = $xmlHandler->getNodeArray("configRecord > configValue");
				
				foreach($dynamicXMLArray as $configValue){
					// Figre out the fields
					if(isset($dynamicFieldStructureArray[$configValue['index']]) && isset($configValue['value'])){
						// We have a link and a valid record
						$dynamicFieldStructureArray[$configValue['index']]['value'] = htmlspecialchars_decode($configValue['value']);
					}
				}
			}
			
			return $dynamicFieldStructureArray;
		}
		
		return NULL;
	}
	
	public function getDynamicFields($text = 0){
		$array = self::getDynamicFieldsArray((isset($this->recordInfo['dynamicFields']) ? $this->recordInfo['dynamicFields'] : NULL), self::getDynamicFieldsConfigString());
		
		if($text == 1){
			$outputString = "";
			if(isset($array) && count($array) > 0){
				if(($dynamicFields = $array) != NULL){
					foreach($dynamicFields as $field){
						if(!isset($field['frontendDisplay']) || (isset($field['frontendDisplay']) && $field['frontendDisplay'] == 1)){
							$value = (isset($field['value']) ? $field['value'] : "");
							
							if($field['type'] === "option"){
								foreach($field['options'] as $optionKey => $optionValue){
									if($field['value'] == $optionKey){
										$value = $optionValue;
									}
								}
							}
							
							$outputString .= "<h4>" . $field['name'] . "</h4>";
							$outputString .= "<div>" . ($field['link'] != 0 ? "<a href=\"" . ($field['link'] == 2 ? "http://" . $value : ($field['link'] == 1 ? $value : "#")) . "\">" . $value . "</a>" : $value) . "</div>";
						}
					}
				}
			}
			
			return $outputString;
		} elseif($text == 2){
			return $array;
		} else {
			return (isset($this->recordInfo['dynamicFields']) ? $this->recordInfo['dynamicFields'] : NULL);
		}
	}
	
	/**
	 * Returns the dynamic field.
	 * If the index is equal to -1 then it returns all of the fields in an XML styled structure
	 * otherwise it returnes the index
	 */
	public function getDynamicField($index){
		if(isset($index)){
			$array = self::getDynamicFieldsArray((isset($this->recordInfo['dynamicFields']) ? $this->recordInfo['dynamicFields'] : NULL), self::getDynamicFieldsConfigString());
			
			if(isset($array)){
				foreach($array as $field){
					if(isset($field['index']) && isset($index) && $field['index'] == $index){
						if(isset($field['value'])){
							return $field['value'];
						}
					}
				}
			}
		}
	
		return "";
	}
	
	/**
	 * Sets the value for a dynamic field
	 */
	public function setDynamicField($index, $inputValue){
		ImportClass("Xml.XMLHandler");
	
		if(isset($index) && isset($inputValue)){
			$xmlHandler = new XMLHandler();
			// Check to make sure configField is in the XML format
			if(!isset($this->recordInfo['dynamicFields']) || $this->recordInfo['dynamicFields'] == ""){
				// Since configField doesn't exist, create a basic structure
				$this->recordInfo['dynamicFields'] = "<configRecord></configRecord>";
			}
				
			// Take the configField and make it into an array
			$xmlHandler->setXMLString($this->recordInfo['dynamicFields']);
			
			// Clean the inputValue
			$scrubbedValue = htmlspecialchars($inputValue);
			
			// Check if the field exists
			if($xmlHandler->nodeExists("configRecord > configValue > index", $index)){
				// The node exists
				// Lets update it
				if($xmlHandler->updateNodeValue("configRecord > configValue", $index, $scrubbedValue) == false){
					return false;
				}
			} else {
				// Create a new config value
				$xmlHandler->addChildNode("configRecord", "configValue");

				// Add the index
				$xmlHandler->addChildNode("configRecord > configValue", "index", $index);

				// Add the value
				$xmlHandler->addChildNode("configRecord > configValue", "value", $scrubbedValue);
			}
			
			// Store it
			$this->recordInfo['dynamicFields'] = $xmlHandler->getXMLString(FALSE);
			
			return true;
		}

		return false;
	}
	
	/**
	 * TODO: This function should be handled by the client and return the config header for the dynamic fields
	 */
	public function getDynamicFieldsConfigString(){
		if(isset($this->dynamicFieldRecord) && is_array($this->dynamicFieldRecord)){
			if(ConfigOption::exists($this->dynamicFieldRecord['component'], $this->dynamicFieldRecord['name'])){
				$cfgOption = new ConfigOption(array("component" => $this->dynamicFieldRecord['component'], "name" => $this->dynamicFieldRecord['name']));
				return $cfgOption->getValue();
			}
		}
	}
	
	/**
	 * This function handles the ajax/api searches for dynamic fields
	 * TODO: Create the function
	 */
	public function searchDynamicFields(){
		
	}
	
	/**
	 * 
	 */
	public function findReplaceEmailString($inputString, $findReplaceArray){
		if(isset($inputString) && isset($findReplaceArray) && strlen($inputString) > 0 && is_array($findReplaceArray)){
			foreach($findReplaceArray as $find => $replace){
				$inputString = str_replace("[" . $find . "]", $replace, $inputString);
			}
		}
		
		return $inputString;
	}
}
?>
