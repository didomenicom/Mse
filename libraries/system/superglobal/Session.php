<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
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