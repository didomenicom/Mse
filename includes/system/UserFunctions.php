<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

/**
 * Functions to handle the user checks/requests globally
 * TODO: Add isLoggedIn() function
 */
class UserFunctions {
	private static $userInfo = NULL;
	
	/**
	 * Returns a User structure if a user is logged in, otherwise NULL
	 */
	public static function getLoggedIn(){
		global $Config;
		
		
		if(UserFunctions::$userInfo == NULL){
			
			// Check if a session exists
			if(Cookie::exists($Config->getSystemVar('cookieName')) == true){
				ImportClass("User.UserSession");
				
				// 1. Find the cookie
				$sessionId = Cookie::get($Config->getSystemVar('cookieName'));
				
				try {
					// 2. Create the UserSession
					$userSession = new UserSession($sessionId);
					
					// 3. Create the user
					ImportClass("User.User");
						
					UserFunctions::$userInfo = new User($userSession->getUserId());
				} catch (MseException $e) {
					// TODO: Remove the cookie
					
				}
			}
		}
		
		if(UserFunctions::$userInfo != NULL){
			return UserFunctions::$userInfo;
		}
		
		return NULL;
	}
	
	/** 
	 * Returns the link to login
	 */
	public static function getLoginUrl($includeBase = true){
		return ($includeBase == true ? (Define::get('baseSystem') == 1 ? Url::getAdminHttpBase() : Url::getHttpBase()) : "") . "/index.php?option=user&act=login";
	}
	
	/** 
	 * Returns the link to logout
	 */
	public static function getLogoutUrl($includeBase = true){
		return ($includeBase == true ? (Define::get('baseSystem') == 1 ? Url::getAdminHttpBase() : Url::getHttpBase()) : "") . "/index.php?option=user&act=logout";
	}
}

?>