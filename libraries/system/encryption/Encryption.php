<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

/**
 * TODO: Implement separate key/iv inline rather than global
 * 		 Enable/disable encryption
 */

class Encryption {
	private static $key = NULL;
	private static $iv = NULL;
	
	public static function encrypt($text, $key = NULL, $iv = NULL){
		if(Encryption::$key != NULL && Encryption::$iv != NULL){
			if(isset($text)){
				if($text !== ""){
					$text = mcrypt_encrypt(MCRYPT_BLOWFISH, Encryption::$key, $text, MCRYPT_MODE_CBC, Encryption::$iv);
					$text = base64_encode($text);
					
					return $text;
				} else {
					Log::warn("Encrypt: encrypt text is empty");
				}
			} else {
				Log::warn("Encrypt: encrypt text is null");
			}
		} else {
			Log::warn("Encrypt: encrypt - " . (Encryption::$key == NULL ? "Key is NULL " : "") . (Encryption::$iv == NULL ? "IV is NULL " : ""));
		}
		
		return NULL;
	}
	
	public static function decrypt($msg, $key = NULL, $iv = NULL){
		if(Encryption::$key != NULL && Encryption::$iv != NULL){
			if(isset($msg)){
				if($msg !== ""){
					$msg = base64_decode($msg);
					
					$msg = mcrypt_decrypt(MCRYPT_BLOWFISH, Encryption::$key, $msg, MCRYPT_MODE_CBC, Encryption::$iv);
					$msg = rtrim($msg, chr(0)); // Removes the padding PHP adds
					
					return $msg;
				} else {
					Log::warn("Encrypt: decrypt msg is empty");
				}
			} else {
				Log::warn("Encrypt: decrypt msg is null");
			}
		} else {
			Log::warn("Encrypt: decrypt - " . (Encryption::$key == NULL ? "Key is NULL " : "") . (Encryption::$iv == NULL ? "IV is NULL " : ""));
		}
		
		return false;
	}
	
	public static function setKey($keyValue){
		Encryption::$key = $keyValue;
	}
	
	public static function setIv($ivValue){
		Encryption::$iv = $ivValue;
	}
}

?>