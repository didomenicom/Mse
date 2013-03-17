<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

ImportClass("SuperGlobal");

class Cookie extends SuperGlobal {
	public static function exists($input){
		return (isset($_COOKIE[$input]) ? true : false);
	}
	
	public static function get($input){
		return (self::exists($input) ? $_COOKIE[$input] : NULL);
	}
	
	public static function set($inputVar, $inputValue){
		if(isset($inputVar) && isset($inputValue)){
			$_COOKIE[$inputVar] = $inputValue;
			
			return true;
		}
		
		return false;
	}
	
	public static function add($inputName, $inputValue, $inputExpire = 0){
		global $Config;
		
		if(isset($inputName) && isset($inputValue) && self::exists($inputName) == false){
			if($inputExpire == 0){
				$inputExpire += time() + time();
			}
			
			$result = setcookie($inputName, $inputValue, $inputExpire, "/", $Config->getVar("cookieDomain"));
			
			if($result == true){
				$_COOKIE[$inputName] = $inputValue;
				
				return true;
			} else {
				// TODO: Report failure
			}
		}
		
		return false;
	}
	
	public static function delete($inputName){
		global $Config;
		
		if(isset($inputName) && self::exists($inputName) == true){
			$result = setcookie($inputName, "", (time() - 3600), "/", $Config->getVar("cookieDomain"));
			
			if($result == true){
				unset($_COOKIE[$inputName]);
				
				return true;
			} else {
				// TODO: Report failure
			}
		}
		
		return false;
	}
}
?>