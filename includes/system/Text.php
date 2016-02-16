<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

/**
 * This class handles all of the text related functions.
 * It also handles the text verification
 */
class Text {
	public static function display($text){
		return stripslashes($text);
	}
	
	public static function store($text){
		return addslashes($text);
	}
	
	public static function sanitize($input){
		$output = NULL;
		
		if(is_array($input) == true){
			foreach($input as $key => $value){
				$output[$key] = addslashes(htmlspecialchars($value));
			}
		} else {
			$input = addslashes(htmlspecialchars($input));
			$output = $input;
		}
		
		return $output;
	}
	
	/**
	 * Displays the text as a page header. Currently supports bootstrap 2
	 * TODO: Remove
	 */
	public static function pageTitle($inputText){
		return "<div class=\"page-header\" style=\"margin-top: 0px; margin-bottom: 5px;\"><h1>" . $inputText . "</h1></div>";
	}
	
	/**
	 * Verifies if a date is in a valid format of something similar to MMDDYYYY
	 * TODO: Move to Date class?
	 */
	public static function verifyStandardDateFormat($inputText){
		return (preg_match("/^((0?[1-9]|1[012])[- \/.](0?[1-9]|[12][0-9]|3[01])[- \/.](19|20)?[0-9]{2})*$/", $inputText) == 1 ? true : false);
	}
	
	/**
	 * Verifies if a date is in a valid format of something similar to YYYYMMDD
	 * TODO: Move to Date class?
	 */
	public static function verifyDbDateFormat($inputText){
		return (preg_match("/^([0-9]{4})(0?[1-9]|1[012])(0?[1-9]|[12][0-9]|3[01])*$/", $inputText) == 1 ? true : false);
	}
	
	/**
	 * Verifies if a date is in a valid format of something similar to YYYYMMDD
	 * TODO: Move to Date class?
	 */
	public static function verifySqlDateTimeFormat($inputText){
		return true; // TODO: Finish
	}
	
	/**
	 * Verifies if an email address is in a valid format similar to email@domain.com
	 */
	public static function verifyEmailAddressFormat($inputText){
		return (preg_match("/^[a-zA-Z0-9._%-+]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/", $inputText) == 1 ? true : false);
	}
	
	/**
	 * Verifies if an input is a integer
	 */
	public static function verifyIntegerFormat($inputText){
		return (preg_match("/^[0-9]+$/", $inputText) == 1 ? true : false);
	}
	
	/**
	 * Verifies if a phone number is in a valid format similar to (123) 456-7890
	 */
	public static function verifyPhoneNumberFormat($inputText){
		return (preg_match("/^(([0-9]{1})*[- .(]*([0-9]{3})[- .)]*[0-9]{3}[- .]*[0-9]{4})+$/", $inputText) == 1 ? true : false);
	}
	
	
	/**
	 * Verifies if a date is in a valid format of something similar to YYYY-MM-DD
	 * TODO: Move to Date class?
	 */
	public static function verifySqlDateFormat($inputText){
		return (preg_match("/^([0-9]{4})-(0?[1-9]|1[012])-(0?[1-9]|[12][0-9]|3[01])*$/", $inputText) == 1 ? true : false);
	}
	
	/**
	 * Verifies if a date is in a valid format of something similar to HH:MM:SS
	 * TODO: Move to Date class?
	 */
	public static function verifySqlTimeFormat($inputText){
		return (preg_match("/^([0-9]{2}):([0-9]{2}):([0-9]{2})*$/", $inputText) == 1 ? true : false);
	}
	
	/**
	 * Verifies if a date is in a valid format of something similar to HHMM or HHMMSS
	 * TODO: Move to Date class?
	 */
	public static function verifyMilitaryTimeFormat($inputText){
		return (preg_match("/^(0?[0-9]|1[0-9]|2[0-4])([0-9]{2})*$/", $inputText) == 1 ? true : (preg_match("/^(0?[0-9]|1[0-9]|2[0-4])([0-9]{2})([0-9]{2})*$/", $inputText) == 1 ? true : false));
	}
	
	/**
	 * Converts an input date in the format of MM/DD/YYYY into a database format of YYYYMMDD
	 * TODO: Move to Date class?
	 */
	public static function convertStandardDateToDbDateFormat($inputText){
		// Check if the input is in standard format
		if(strlen($inputText) > 0 && Text::verifyStandardDateFormat($inputText) == true){
			$timestamp = DateTime::createFromFormat('m/d/Y', $inputText);
			return $timestamp->format('Ymd');
		}
		
		return NULL;
	}
	
	/**
	 * Converts an input date in the format of YYYYMMDD into a format of MM/DD/YYYY
	 * TODO: Move to Date class?
	 */
	public static function convertDbDateToStandardDateFormat($inputText){
		// Check if the input is in standard format
		if(Text::verifyDbDateFormat($inputText) == true){
			return substr($inputText, 4, 2) . "/" . substr($inputText, 6, 2) . "/" . substr($inputText, 0, 4);
		}
		
		return "";
	}
	
	/**
	 * Converts an input date in the format of MM/DD/YYYY into a sql database format (Y-m-d) of YYYY-MM-DD
	 * TODO: Move to Date class?
	 */
	public static function convertStandardDateToSqlDateFormat($inputText){
		$timestamp = DateTime::createFromFormat('m/d/Y', $inputText);
		return $timestamp->format('Y-m-d');
	}
	
	/**
	 * Converts an input date in the format of MM/DD/YYYY into a sql database format (Y-m-d H:i:s) of YYYY-MM-DD HH:MM:SS 
	 * TODO: Move to Date class?
	 */
	public static function convertStandardDateToSqlDateTimeFormat($inputText, $includeTime = true){
		// Check if the input is in standard format
		if(Text::verifyStandardDateFormat($inputText) == true){
			return substr($inputText, 4, 2) . "-" . substr($inputText, 6, 2) . "-" . substr($inputText, 0, 4) . ($includeTime == true ? " 00:00:00" : "");
		}
	
		return "";
	}
	
	/**
	 * Converts an input date in the format of YYYYMMDD into a sql database format (Y-m-d H:i:s) of YYYY-MM-DD HH:MM:SS
	 * TODO: Move to Date class?
	 */
	public static function convertDbDateToSqlDateTimeFormat($inputText, $includeTime = true){
		// Check if the input is in standard format
		if(Text::verifyDbDateFormat($inputText) == true){
			return substr($inputText, 0, 4) . "-" . substr($inputText, 4, 2) . "-" . substr($inputText, 6, 2) . ($includeTime == true ? " 00:00:00" : "");
		}
	
		return "";
	}
	
	/**
	 * Converts an input date in the format of YYYYMMDD into a sql database format (Y-m-d) of YYYY-MM-DD
	 * TODO: Move to Date class?
	 */
	public static function convertDbDateToSqlDateFormat($inputText){
		// Check if the input is in standard format
		if(Text::verifyDbDateFormat($inputText) == true){
			return substr($inputText, 0, 4) . "-" . substr($inputText, 4, 2) . "-" . substr($inputText, 6, 2);
		}
	
		return "";
	}
	
	/**
	 * Converts an input date in the format of YYYY-MM-DD HH:MM:SS into a php time() format
	 * TODO: Move to Date class? 
	 */
	public static function convertSqlDateTimeToPhpTimeFormat($inputText){
		return strtotime($inputText, Date::getCurrentTimeStamp());
	}
	
	/**
	 * Checks if an input string is standard characters
	 * Standard characters are a-z, A-Z, 0-9, '.', '-', '_', and ' '
	 */
	public static function verifyStandardCharacters($inputText){
		return (preg_match("/^[a-zA-Z0-9\.\-\_\s]+$/", $inputText) == 1 ? true : false);
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
	 * Converts a string into bytes
	 * For example, 30M is equal to 30 MB and in turn 30000000
	 * Supported units are B, K, M, G
	 * @param unknown_type $inputString
	 */
	public static function convertFilesizeStringToBytes($inputString){
		if(strlen($inputString) > 0){
			// Figure out the unit
			$unit = substr($inputString, (strlen($inputString) - 1), 1);
			$value = (int)substr($inputString, 0, (strlen($inputString) - 1));
			
			switch($unit){
				case "B":
					return ($value * pow(1000, 0));
					break;
				case "K":
					return ($value * pow(1000, 1));
					break;
				case "M":
					return ($value * pow(1000, 2));
					break;
				case "G":
					return ($value * pow(1000, 3));
					break;
				default:
					break;
			}
		}
		
		return 0;
	}
	
	/**
	 * Converts a true/false number into Yes/No text
	 */
	public static function getYesNo($inputNumber){
		switch($inputNumber){
			case 0:
				return "No";
				break;
			case 1:
				return "Yes";
				break;
			default:
				Log::warn("Text.getYesNo() -- Unknown value - '" . $inputNumber . "'");
				return "Unknown";
				break;
		}
	}
	
	/**
	 * Converts a true/false number into True/False text
	 */
	public static function getTrueFalse($inputNumber){
		switch($inputNumber){
			case 0:
				return "False";
				break;
			case 1:
				return "True";
				break;
			default:
				Log::warn("Text.getTrueFalse() -- Unknown value - '" . $inputNumber . "'");
				return "Unknown";
				break;
		}
	}
	
	/**
	 * Takes a number and formats into a USD currency format
	 */
	public static function formatCurrencyNumber($inputValue, $displayUSDSymbol = true){
		if(isset($inputValue)){
			return ($displayUSDSymbol == true ? "$" : "") . number_format($inputValue, 2, '.', ',');
		}
	}
	
	/** 
	 * This function will handle all of the sorting for table headers. 
	 * TODO: REMOVE/Cleanup
	 */
	public static function sortHeader($displayName, $sorterCall, $sort = array()){
		if(isset($displayName) && isset($sorterCall) && strlen($displayName) > 0 && strlen($sorterCall) > 0){
			if(isset($sort['by']) && isset($sort['direction'])){
				if(strtoupper($sort['direction']) === "ASC" || strtoupper($sort['direction']) === "DESC"){
					return $displayName . ($sort['by'] === $sorterCall ? (strtoupper($sort['direction']) === "ASC" ? " <i class=\"icon-arrow-down\"></i>" : " <i class=\"icon-arrow-up\"></i>") : "");
				}
			}
			
			return $displayName;
		}
	}
	
	/**
	 * TODO: REMOVE
	 * @param unknown_type $ordering
	 * @param unknown_type $id
	 * @param unknown_type $name
	 * @param unknown_type $default
	 */
	public static function ordering($ordering, $id, $name, $default = 0){
		global $Render;
		
		if($default == 1 && !$ordering->id || $ordering->id === $id){
			if($ordering->direction === 'DESC'){
				$Render->addContent("<a href=\"#\" onClick=\"reOrder('" . $id . "', 'ASC');\">" . $name . "</a> <img src=\"" . ROOT_HTTP.TEMPLATE . "/images/downArrow.png\" alt=\"Order Down\" />");
			} elseif($ordering->direction === 'ASC'){
				$Render->addContent("<a href=\"#\" onClick=\"reOrder('" . $id . "', 'DESC');\">" . $name . "</a> <img src=\"" . ROOT_HTTP.TEMPLATE . "/images/upArrow.png\" alt=\"Order Down\" />");
			} else {
				$Render->addContent("<a href=\"#\" onClick=\"reOrder('" . $id . "', 'ASC');\">" . $name . "</a> <img src=\"" . ROOT_HTTP.TEMPLATE . "/images/downArrow.png\" alt=\"Order Down\" />");
			}
		} else {
			$Render->addContent("<a href=\"#\" onClick=\"reOrder('" . $id . "', 'ASC');\">" . $name . "</a>");
		}
	}
	
	
	/**
	 * TODO: REMOVE
	 * @param unknown_type $filter
	 * @param unknown_type $name
	 * @param unknown_type $options
	 * @param unknown_type $all
	 */
	public static function filters($filter, $name, $options, $all = 1){
		global $Render;
		
		$Render->addContent("<select name=\"filter_" . $name . "\" onchange=\"document.adminForm.submit();\">");
		
		if($all == 1){
			$Render->addContent("<option value=\"0\"");
			if(!$filter){ $Render->addContent(" selected=selected"); }
			$Render->addContent(">ALL</option>");
		}
		
		foreach($options as $option){
			$Render->addContent("<option value=\"" . $option[0] . "\"");
			if($filter == $option[0]){ $Render->addContent(" selected=selected"); }
			$Render->addContent(">" . $option[1] . "</option>");
		}
		
		$Render->addContent("</select>");

	}

	/**
	 * TODO: REMOVE
	 * @param unknown_type $pageLength
	 * @param unknown_type $options
	 */
	public static function displayCount($pageLength, $options = array(10, 25, 50, 100)){
		global $Render;
		
		$Render->addContent("Display #<select name=\"pageLength_pageCount\" onchange=\"document.adminForm.submit();\">
			<option value=\"0\"");
			if(!$pageLength->pageCount){ $Render->addContent(" selected=selected"); }
			$Render->addContent(">All</option>");
			
			foreach($options as $option){
				$Render->addContent("<option value=\"" . $option . "\"");
				if($pageLength->pageCount == $option){ $Render->addContent(" selected=selected"); }
				$Render->addContent(">" . $option . "</option>");
			}
		$Render->addContent("</select>");
	}
	
	/**
	 * TODO: REMOVE
	 * @param unknown_type $pageLength
	 * @param unknown_type $rowsCount
	 */
	public static function displayPageNumber($pageLength, $rowsCount){
		global $Render;
		
		if($pageLength->pageCount){
			if($rowsCount > $pageLength->pageCount){
				$totalCount = ceil($rowsCount / $pageLength->pageCount);
				
				$Render->addContent("Page #<select name=\"pageLength_pageId\" onchange=\"document.adminForm.submit();\">");
				
				for($i = 0; $i < $totalCount; $i++){
					$Render->addContent("<option value=\"" . $i . "\"");
					if($pageLength->pageId == $i){ $Render->addContent(" selected=selected"); }
						$Render->addContent(">" . ($i + 1) . "</option>");
					}
				$Render->addContent("</select>");
			}
		}
	}
	
	/**
	 * This function will find and replace characters/substrings in a string. 
	 */
	public static function findReplaceString($inputString, $findReplaceArray){
		if(isset($inputString) && isset($findReplaceArray) && strlen($inputString) > 0 && is_array($findReplaceArray)){
			foreach($findReplaceArray as $find => $replace){
				$inputString = str_replace($find, $replace, $inputString);
			}
		}
	
		return $inputString;
	}
	
	/**
	 * 
	 */
	public static function sanitizeXMLData($inputString){
		$array = array("&" => "&amp;");
		return Text::findReplaceString($inputString, $array);
	}
}
?>
