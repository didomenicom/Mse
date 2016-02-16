<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

/**
 * Functions to handle the user checks/requests globally
 * TODO: Add isLoggedIn() function
 */
class UserFunctions {
	private static $userInfo = NULL;
	
	/**
	 * Returns the user id if one exists, otherwise NULL
	 */
	public static function getUserId(){
		global $Config;
	
		// Check if a session exists
		if(Cookie::exists($Config->getSystemVar('cookieName')) == true){
			// Import the classes manually so the logs work
			require_once BASEPATH.LIBRARY.SYSTEM . "/user/UserSession.php";
			
			// 1. Find the cookie
			$sessionId = Cookie::get($Config->getSystemVar('cookieName'));
			
			try {
				// 2. Create the UserSession
				$userSession = new UserSession($sessionId);
				
				// 3. Return the user id
				return $userSession->getUserId();
			} catch (MseException $e){
				// Destroy the cookie
				Cookie::delete($Config->getSystemVar('cookieName'));
				
				// Success
				Url::redirect((Define::get('baseSystem') == 1 ? Url::getAdminHttpBase() : Url::getHttpBase()), 0, false);
			}
		}
	
		if(UserFunctions::$userInfo != NULL){
			return UserFunctions::$userInfo;
		}
	
		return NULL;
	}
	
	/**
	 * Returns a User structure if a user is logged in, otherwise NULL
	 */
	public static function getLoggedIn(){
		global $Config;
		
		if(UserFunctions::$userInfo == NULL){
			// Check if a session exists
			if(Cookie::exists($Config->getSystemVar('cookieName')) == true){
				ImportClass("User.User");
				// Get the user id
				$userId = self::getUserId();
				
				// Create the user
				UserFunctions::$userInfo = new User($userId);
				
				// Check to make sure this user is not deleted
				if(UserFunctions::$userInfo->getDeleted() != 0){
					UserFunctions::$userInfo = NULL;
					
					// Destroy the cookie
					Cookie::delete($Config->getSystemVar('cookieName'));
				}
			}
		}
		
		if(UserFunctions::$userInfo != NULL){
			return UserFunctions::$userInfo;
		}
		
		return NULL;
	}
	
	/**
	 * This function finds out if the user has access to the component. 
	 * It also checks to see if the user is logged in
	 */
	public static function hasComponentAccess($component, $function){
		if(strlen($component) > 0 && strlen($function) > 0){
			// Find out if the user is logged in
			if(($user = UserFunctions::getLoggedIn()) != null){
				// Get the users permission group
				$permissionGroup = $user->getPermissionGroup(2);
				
				// Find out if there is access
				return $permissionGroup->getComponentFunction($component, $function);
			}
		}
		
		return false;
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
