<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

class Url {
	private static $root = "";
	private static $dirRoot = "";
	private static $urlParts = array();
	
	/**
	 * Returns the HTTP url for the system frontend
	 */
	public static function getHttpBase($text = NULL){
		return self::getRoot() . ($text !== NULL ? DS : "") . $text;
	}
	
	/**
	 * Returns the HTTP url for the system backend
	 */
	public static function getAdminHttpBase($text = NULL){
		return self::getRoot().DS."administrator" . ($text != NULL ? DS : "") . $text;
	}
	
	/**
	 * Returns the HTTP url of the current page
	 */
	public static function getCurrentHttp(){
		$parts = explode("/", $_SERVER['REQUEST_URI']);
		return array_pop($parts);
	}
	
	/**
	 * Returns the directory path for the system frontend
	 */
	public static function getDirBase($text = NULL){
		return BASEPATH . ($text != NULL ? DS : "") . $text;
	}
	
	/**
	 * Returns the directory path for the system backend
	 */
	public static function getAdminDirBase($text = NULL){
		return BASEPATH.DS."administrator" . ($text != NULL ? DS : "") . $text;
	}
	
	/**
	 * Creates the http url for the system root (frontend)
	 */
	private static function getRoot(){
		if(self::$root == ""){
			self::$root = "http://" . $_SERVER['HTTP_HOST'] . self::getDirectory();
		}
		
		return self::$root;
	}
	
	/**
	 * Creates the directory path for the system root (frontend)
	 */
	private static function getDirRoot(){
		if(self::$dirRoot == ""){
			self::$dirRoot = $_SERVER['DOCUMENT_ROOT'] . self::getDirectory();
		}
		
		return self::$dirRoot;
	}
	
	/**
	 * Finds the directory path from the root of the system
	 */
	private static function getDirectory(){
		$dir = "";
		// Figure out directory
		// TODO: This is wrong... fix it
		if(substr(BASEPATH, 0, strlen($_SERVER['DOCUMENT_ROOT'])) === $_SERVER['DOCUMENT_ROOT']){
			// Remove base 
			$dir = substr(BASEPATH, strlen($_SERVER['DOCUMENT_ROOT']), strlen(BASEPATH));
		}
		
		return $dir;
	}
	
	/**
	 * Returns to the frontend home
	 */
	public static function home(){
		return (Define::get('baseSystem') == 1 ? self::getAdminHttpBase() : self::getHttpBase()) . "/index.php";
	}
	
	/**
	 * This function grabs all of the header stuff located in the url ($_GET)
	 * If text is passed in, it returns the text if it exists, NULL otherwise
	 * If no text is passed in, it returns the entire array, no matter how small
	 */
	public static function getParts($text = NULL){
		if(count(self::$urlParts) == 0){
			// Get all of the parts of the url
			foreach($_GET as $key => $value){
				// Cleanup the parts
				$key = strip_tags($key);
				$value = strip_tags($value);
				
				self::$urlParts[$key] = $value;
			}
		}
		
		if($text != NULL && $text != ""){
			if(isset(self::$urlParts[$text])){
				return self::$urlParts[$text];
			} else {
				return NULL;
			}
		} else {
			return self::$urlParts;
		}
	}
	
	/**
	 * Redirects to a url
	 * TODO: Fix admin to front end redirect and vise versa
	 */
	public static function redirect($location, $timeout = 0, $internal = true){
		if(isset($location) && $location != "" && $timeout >= 0){
			if($timeout == 0){
				// If there is any session stuff, close it so its not lost
				session_write_close();
				
				// Redirect
				header("Location: " . ($internal == true ? (Define::get('baseSystem') == 1 ? self::getAdminHttpBase() : self::getHttpBase()) : "") . $location);
				
				// Kill any code after the redirect
				exit();
			} else {
				echo "<META HTTP-EQUIV='Refresh' CONTENT='" . $timeout . "; URL=" . ($internal == true ? (Define::get('baseSystem') == 1 ? self::getAdminHttpBase() : self::getHttpBase()) : "") . $location . "'>";
			}
		}
		
		return false;
	}
}

?>