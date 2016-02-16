<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

/**
 * This class handles all of the date related stuff.
 * Date verification and display stuff is in the Text class
 */
class Date {
	/**
	 * Sets the default timezone for all of the code
	 * Based on the timezone defined in the configuration. 
	 * TODO: Remove the config timezone and pass it in 
	 */
	public static function setSystemTimezone(){
		global $Config;
		date_default_timezone_set($Config->getSystemVar('systemTimezone'));
	}
	
	/**
	 * Returns the current timestamp in seconds from Jan 1, 1970
	 */
	public static function getCurrentTimeStamp(){
		return time();
	}
	
	/**
	 * Returns a formatted date for an SQL datetime type field. 
	 * If a timestamp is inputted it will calculate based on that time, 
	 * otherwise it is based on the current timestamp
	 */
	public static function getDbDateTimeFormat($inputDate = 0){
		if($inputDate == 0){
			return date("Y-m-d H:i:s", Date::getCurrentTimeStamp());
		} else {
			return date("Y-m-d H:i:s", $inputDate);
		}
	}
}

?>
