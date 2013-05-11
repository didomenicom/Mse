<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

class ConfigTypes {
	private static $records = array(
				"Unknown", 
				"Integer", 
				"Double",
				"Array", 
				"Text", 
				"Date");
	
	private static $recordIndex = 1;

	public static function reset(){
		return $this->recordIndex = 1;
	}

	public static function hasNext(){
		return (self::$recordIndex < count(self::$records) ? true : false);
	}

	public static function getNext(){
		$result = array("id" => self::$recordIndex, "name" =>self::$records[self::$recordIndex]);
		self::$recordIndex++;
		return $result;
	}

	public static function rowsExist(){
		return (count(self::$records) > 0);
	}

	public static function getName($id){
		return stripslashes(self::$records[$id]);
	}
}

?>