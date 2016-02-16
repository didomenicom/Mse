<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2013 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

class XMLParser {
	private static $valueArray = array();
	private static $keyArray = array();
	private static $parsed = array();
	private static $index = 0;
	private static $attribKey = 'attributes';
	private static $valueKey = 'value';

	/**
	 * Parse an XML string
	 * Return array with contents or empty array if failure or no contents
	 */
	public static function parse($xml = NULL){
		if(!is_null($xml)){
			self::$index = 0;
				
			$parser = xml_parser_create();
				
			xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
			xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
			if(!(bool)xml_parse_into_struct($parser, $xml, self::$valueArray, self::$keyArray)){
				return NULL;
			}
			xml_parser_free($parser);
				
			self::$parsed = self::parse_recurse();
		}
	
		return self::$parsed;
	}
	
	private static function parse_recurse(){        
		$found = array();
		$tagCount = array();
		
		while(isset(self::$valueArray[self::$index])){
			$tag = self::$valueArray[self::$index];
			$tagName = $tag['tag'];
			self::$index++;
			
			if($tag['type'] == 'close'){
				return $found;
			}
			
			if(isset($tagCount[$tagName])){        
				if($tagCount[$tagName] == 1){
					$found[$tagName] = array($found[$tagName]);
				}
				
				$tagRef = &$found[$tagName][$tagCount[$tagName]];
				$tagCount[$tagName]++;
			} else {
				$tagCount[$tagName] = 1;
				$tagRef = &$found[$tagName];
			}
			
			switch($tag['type']){
				case 'open':
					$tagRef = self::parse_recurse();
					
					if(isset($tag['attributes'])){
						$tagRef[self::$attribKey] = $tag['attributes'];
					}
					break;
				case 'complete':
					if(isset($tag['attributes'])){
						$tagRef[self::$attribKey] = $tag['attributes'];
						$tagRef = &$tagRef[self::$valueKey];
					}
					
					if(isset($tag['value'])){
						$tagRef = $tag['value'];
					}
					break;
			}            
		}
		
		return $found;
	}
	
	/**
	 * This takes an array and returns a string in XML format
	 * 
	 * TODO: Fix this function. There are plenty of test cases that will break it. 
	 */
	public static function generateXML($inputArray = NULL){
		return NULL; // TODO: THIS FUNCTION SHOULD NEVER BE CALLED
		$outputString = "";
		
		// Check if there was an input
		if(!is_null($inputArray)){
			// Check if it is an array
			if(is_array($inputArray)){
				// This is an array, process it
				// Loop through each item in the array
				foreach($inputArray as $key => $value){
					// Check if the content of this array item is an array
					if(is_array($value)){
						// This is an array
						// Check if this value array has multiple records in it (numbered indexes)
						$numberedIndexes = 0;
						
						foreach($value as $valueKey => $valueValue){
							if(is_int($valueKey)){
								$numberedIndexes++;
							}
						}
						
						if($numberedIndexes == count($value)){
							foreach($value as $valueKey => $valueValue){
								$outputString .= "<" . $key . ">" . self::generateXML($valueValue) . "</" . $key . ">";
							}
						} else {
							$outputString .= "<" . $key . ">" . self::generateXML($value) . "</" . $key . ">";
						}
					} else {
						// This is not an array, store the value to be returned
						$outputString .= "<" . $key . ">" . strip_tags(htmlspecialchars($value)) . "</" . $key . ">";
					}
				}
			} else {
				// This is a string so return it
				$outputString = $inputArray;
			}
		}
		
		return $outputString;
	}
}

?>
