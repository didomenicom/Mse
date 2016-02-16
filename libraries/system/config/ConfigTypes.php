<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

/**
 * TODO: 
 * 		Add boolean
 * 		Add permission group
 */
class ConfigTypes {
/*
 * NOTES: configHeader can be embedded inside of configHeader
<?xml version="1.0"?>
<configHeader>
	<index></index>
	<displayText></displayText>
	<option></option>
</configHeader>
<configRecord>
	<configValue>
		<index></index>
		<value></value>
	</configValue>
</configRecord>
<configRecord>
	<configValue>
		<index></index>
		<value></value>
	</configValue>
</configRecord>
 */
	/*
	 * Options:
	 * 1. 	Integer - 
	 * 			MaxLength=<number>
	 * 2. 	Double - 
	 * 			MaxLength=<number>|DecimalLength=<number>
	 * 3. 	Array - 
	 * 			Options Format: DisplayName(indexName=<type(options)>)|DisplayName(indexName=<type(options)>)
	 * 			Value Format: (Value|Value),(Value|Value)
	 * 4. 	InputBox - 
	 * 			MaxLength=<number>
	 * 5. 	TextArea - 
	 * 			WYSIWYG=<1|0>
	 * 6. 	Date - 
	 * 			Format
	 * 7. 	Option - 
	 * 			DisplayName(value)|DisplayName(value)
	 * 8. 	True/False
	 * 			T/F or Y/N or O/O
	 */
	private static $records = array(
				"Unknown",  	// 0
				"Integer",  	// 1
				"Double",   	// 2
				"Array",    	// 3
				"TextBox",  	// 4
				"TextArea", 	// 5
				"Date",     	// 6
				"Option",   	// 7
				"True/False"); 	// 8
	
	private static $recordIndex = 1;

	// TODO: Change it to a stack
	public static function reset(){
		return self::$recordIndex = 1;
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
