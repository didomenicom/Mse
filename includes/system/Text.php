<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
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
	
	public static function pageTitle($inputText){
		return "<div class=\"page-header\" style=\"margin-top: 0px; margin-bottom: 5px;\"><h1>" . $inputText . "</h1></div>";
	}
	
	/**
	 * Verifies if a date is in a valid format of something similar to MMDDYYYY
	 */
	public static function verifyStandardDateFormat($inputText){
		return (preg_match("/^((0?[1-9]|1[012])[- \/.](0?[1-9]|[12][0-9]|3[01])[- \/.](19|20)?[0-9]{2})*$/", $inputText) == 1 ? true : false);
	}
	
	/**
	 * Verifies if a date is in a valid format of something similar to YYYYMMDD
	 */
	public static function verifyDbDateFormat($inputText){
		return (preg_match("/^([0-9]{4})(0?[1-9]|1[012])(0?[1-9]|[12][0-9]|3[01])*$/", $inputText) == 1 ? true : false);
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
	 * Converts an input date in the format of MM/DD/YYYY into a database format of YYYYMMDD
	 */
	public static function convertStandardDateToDbDateFormat($inputText){
		// Check if the input is in standard format
		if(Text::verifyStandardDateFormat($inputText) == true){
			$inputText = preg_replace_callback('/((0?[1-9]|1[012])[- \/.](0?[1-9]|[12][0-9]|3[01])[- \/.]((19|20)?[0-9]{2}))/', 
								function($matches){
									return (strlen($matches[4]) == 2 ? "20" : "") . $matches[4] . (strlen($matches[2]) == 1 ? "0" : "") . $matches[2] . (strlen($matches[3]) == 1 ? "0" : "") . $matches[3]; 
								}, 
							$inputText);
			return $inputText;
		}
		
		return "";
	}
	
	/**
	 * Converts an input date in the format of YYYYMMDD into a database format of MM/DD/YYYY
	 */
	public static function convertDbDateToStandardDateFormat($inputText){
		// Check if the input is in standard format
		if(Text::verifyDbDateFormat($inputText) == true){
			return substr($inputText, 4, 2) . "/" . substr($inputText, 6, 2) . "/" . substr($inputText, 0, 4);
		}
	
		return "";
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


}
?>