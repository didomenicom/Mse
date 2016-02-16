<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

ImportClass("SuperGlobal");

class Session extends SuperGlobal {
	public static function get($input){
		return (isset($_SESSION[$input]) ? $_SESSION[$input] : NULL);
	}
	
	public static function set($inputVar, $inputValue){
		if(isset($inputVar) && isset($inputValue)){
			$_SESSION[$inputVar] = $inputValue;
		}
	}
}
?>
