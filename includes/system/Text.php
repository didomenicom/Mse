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
	public function dsp($text){
		return stripslashes($text);
	}
	
	public function store($text){
		return addslashes($text);
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
		return (preg_match("/^(19|20)?[0-9]{2})[- \/.]((0?[1-9]|1[012])[- \/.](0?[1-9]|[12][0-9]|3[01])*$/", $inputText) == 1 ? true : false);
	}
	
	/** 
	 * This function will handle all of the sorting for table headers. 
	 */
	public static function sortHeader($displayName, $sorterCall, $sortBy){
		if(isset($displayName) && isset($sorterCall) && $displayName !== "" && $sorterCall !== ""){
			return $displayName . ($sortBy == true ? "*" : "");
		}
	}
	
	
	
	
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