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
 * 		Db Functions
 * 		Install Script: CREATE TABLE passwordPusher (seccred TEXT, id VARCHAR(128) NOT NULL PRIMARY KEY, ctime DATETIME, xtime DATETIME, views INT, xviews INT);
 * 						CREATE EVENT passwordPusher_cleanup ON SCHEDULE EVERY 5 MINUTE DO UPDATE gtanatio_errors.passwordPusher SET seccred=NULL WHERE xtime<UTC_TIMESTAMP() OR views>=xviews;
 */

class PHPasswordPusher {
	/**
	 * Generates a UUID v5 (SHA-1) 
	 *
	 * @return string $uniqueId
	 */
	public static function getUniqueId(){
		// Generate id in the format (8)-(4)-(4)-(4)-(12)
		ImportClass("Miscellaneous.Miscellaneous");
		return Miscellaneous::generateRandomString(8) . "-" . Miscellaneous::generateRandomString(4) . "-" . Miscellaneous::generateRandomString(4) . "-" . Miscellaneous::generateRandomString(4) . "-" . Miscellaneous::generateRandomString(12);
//		uuid_create(&$context);
//		uuid_create(&$namespace);
		
		
		// Creates a UUID based on a unique ID based on time in milliseconds.
		// The uniqid function is using the more_entropy = true option.
//		uuid_make($context, UUID_MAKE_V5, $namespace, uniqid('',true));
//		uuid_export($context, UUID_FMT_STR, &$uniqueId);
//		return trim($uniqueId);
	}
	
	/**
	 * Hashes the id via bcrypt
	 *
	 * @param string $id
	 *
	 * @return string $hashedId
	 */
	public static function hashId($id, $salt){
		$hashedId = crypt($id, "$2a$07$" . $salt . "$");
		return $hashedId;
	}
	
	/**
	 * Generates a 128-bit salt
	 *
	 * @return string $salt
	 */
	public static function getSalt(){
		$salt = substr(str_replace('+', '.', base64_encode(pack('N4', mt_rand(), mt_rand(), mt_rand(), mt_rand()))), 0, 22);
		return $salt;
	}
	
	/**
	 * Remove a specific record
	 *
	 * @param string $id record to remove
	 *
	 * @return none
	 */
	public static function eraseCred($id){
		$query = "DELETE FROM phpasspush WHERE id=:id";
		$params = array('id' => $id);
		
		// TODO: Add db query
	}
	
	/**
	 * Calculate the expiration time 
	 *
	 * @param integer $minutes minutes to be converted 
	 *
	 * @return string $timePhrase human-readable time phrase
	 */
	public static function calcExpirationDisplay($minutes){
		// The phrase that communicates a human-readable time breakdown
		$timePhrase = '';
		
		// Determine rough breakdown of time between days, hours, and minutes.
		$days = floor($minutes / 1440);
		$hours = floor(($minutes - $days * 1440) / 60);
		$minutes = $minutes - ($days * 1440) - ($hours * 60);
		
		// Determine days
		if($days > 0){
			$timePhrase .= $days . " day";
			
			if($days > 1){
				$timePhrase .= 's';
			}
		}
		
		// Determine if there are leftover hours and minutes
		if($days > 0 && ($hours + $minutes) > 0){
			$timePhrase .= ' + ';
		}
		
		// Determine hours
		if($hours > 0){
			$timePhrase .= $hours . " hour";
			
			if($hours > 1){
				$timePhrase .= 's';
			}
		}
		
		// Determine if there are leftover minutes
		if($hours > 0 && $minutes > 0){
			$timePhrase .= ' + ';
		}
		
		// Determine minutes
		if($minutes > 0){
			$timePhrase .= $minutes . " minute";
			
			if($minutes > 1){
				$timePhrase .= 's';
			}
		}
		
		return $timePhrase;
	}
	
	/**
	 * Sanitize all the inputs and determine their validity.
	 *
	 * @param array $arguments user input
	 *
	 * @return array arguments|boolean failure
	 */
	public static function checkInput($arguments){
		include 'config.php';
		
		// Check the credential
		if(isset($arguments['cred'])){
			$arguments['cred'] = sanitizeCred($arguments['cred']);
			
			if($arguments['cred'] == false){
				$arguments['func'] = 'none';
				print getError("Please enter the secret (whatever it may be)!");
				return false;
			}
		}
		
		// Check Minutes
		if(isset($arguments['time'])){
			// Set to the default value if empty
			if(empty($arguments['time'])){
				$arguments['time'] = $expirationTimeDefault;
			}
			
			// Sanitize the input
			$arguments['time'] = sanitizeNumber($arguments['time']);
			if($arguments['time'] == false){ 
				print getError("Please enter a valid time limit (positive whole number)!");
				return false;
			}
		
			// Apply unit conversion
			if(isset($arguments['units'])){
				switch($arguments['units']){
					case "minutes":
						// Do nothing, as time is already stored in minutes.
						break;
					case "hours":
						// Convert hours to minutes
						$arguments['time'] = ($arguments['time'] * 60);
						break;
					case "days":
						// Convert days to minutes
						$arguments['time'] = ($arguments['time'] * 60 * 24);
						break;
				}                    
			}
			
			// Check against maximum lifetime
			if($arguments['time'] > $credMaxLife){
				print getError("Please enter a time limit fewer than " . calcExpirationDisplay($credMaxLife) . " in the future!");
				return false;
			}
		}
		
		// Check Views
		if(isset($arguments['views'])){
			// Set to the default value if empty
			if(empty($arguments['views'])){
				$arguments['views'] = $expirationViewsDefault;
			}
			
			// Sanitize the input
			$arguments['views'] = sanitizeNumber($arguments['views']);
			
			if($arguments['views'] == false){
				print getError("Please enter a valid view limit (positive whole number)!");
				return false;
			}
		}
		
		// Check Email
		if(isset($arguments['destemail'])){
			// Ignore if empty
			if(empty($arguments['destemail'])){
				print getWarning("No email address was entered, so no email has been sent.");
			} else {
				$arguments['destemail'] = sanitizeEmail($arguments['destemail']);
				
				if($arguments['destemail'] == false){
					print getWarning("Please enter a valid email address!");
					return false;
				}
			}
		}
		
		return $arguments;
	}
	
	/**
	 * Check and Sanitize the user's email.
	 *
	 * @param string $email email address to be sanitized
	 *
	 * @return string $email|boolean failure sanitized email or failure
	 */
	public static function sanitizeEmail($email){
		if(strlen($email) < 50 && filter_var($email, FILTER_VALIDATE_EMAIL)){
			$email = strip_tags($email);
			$email = mysql_real_escape_string($email);
			return $email;
		}
		
		return false;
	}
	
	/**
	 * Sanitize number entry
	 *
	 * @param integer $number number to be sanitized
	 *
	 * @return $number|boolean failure sanitized number or failure
	 */
	public static function sanitizeNumber($number){
		if(filter_var($number, FILTER_VALIDATE_INT) && $number > 0){
			return $number;
		}
		
		return false;
	}
	
	
	
	
	
	/**
	 * Check and Sanitize the user's credentials.
	 *
	 * @param string $cred credential to be sanitized
	 *
	 * @return string $cred|boolean failure sanitized credential or failure
	 */
	public static function sanitizeCred($cred){
		if(!empty($cred)){
			$cred = htmlspecialchars($cred);
			return $cred;
		}
		
		return false;
	}
	
	
	
	/**
	 * Insert the credential into the database
	 *
	 * @param string  $id              the ID string for the credential
	 * @param string  $encrypted       the encrypted credential
	 * @param integer $expirationTime  minutes until expiration
	 * @param integer $expirationViews views until expiration
	 *
	 * @return none
	 */
	public static function insertCredential($id, $encrypted, $expirationTime, $expirationViews){
		global $Config, $db;
		// Set up query
		$query = "INSERT INTO passwordPusher (id, seccred, ctime, views, xtime, xviews) VALUES (" . 
				":id, " . 
				":seccred, " . 
				"UTC_TIMESTAMP(), " . 
				"0, " . 
				"UTC_TIMESTAMP()+ INTERVAL :xtime MINUTE, " . 
				":xviews)";
		
		$params = array('id'        => $id,
						'seccred'   => $encrypted,
						'xtime'     => "+" . (is_numeric($expirationTime) ? $expirationTime : $Config->getSystemVar("expirationTimeDefault")) . " minutes",
						'xviews'    => (is_numeric($expirationViews) ? $expirationViews : $Config->getSystemVar("expirationViewsDefault"))
						);
		
		$result = $db->insert($query, $params);
		
		// Erase all expired entries for good measure.
		PHPasswordPusher::eraseExpiredEntries();
		
		return $result;
	}
	
	
	/**
	 * Erase all records that have expired due to expiration time or view limit
	 * @param PDO $db database connection instance
	 * @return none
	 */
	private static function eraseExpiredEntries(){
		global $db;
		$query = "DELETE FROM passwordPusher WHERE xtime < UTC_TIMESTAMP() OR xviews <= views";
		
		$db->delete($query);
	}
	
	public static function createCredential($inputData){
		global $Config, $db; 
		
		if(isset($inputData) && $inputData !== ""){
			$execute = true;
			while($execute == true){
				$uniqueId = PHPasswordPusher::getUniqueId();
				$result = $db->fetchAssoc("SELECT * FROM passwordPusher WHERE id='" . $uniqueId . "'");
				
				if(isset($result->id) && $result->id === $uniqueId){
					// TODO: Log this hit
				} else {
					$execute = false;
				}
			}
			
			if(PHPasswordPusher::insertCredential($uniqueId, $inputData, $Config->getSystemVar('expirationTimeDefault'), $Config->getSystemVar('expirationViewsDefault')) == true){
				return $uniqueId;
			}
		}
		
		return NULL;
	}
	
	/** 
	 * Retrieve credentials from database
	 * @param string $id the ID string of the credential (token)
	 * @return true on success, false on exceeded gets, NULL on failure
	 */
	public static function retrieveCredential($inputToken, &$outputData){
		global $db;
		if(isset($inputToken) && $inputToken !== ""){
			$data = NULL;
			$updateQuery = "UPDATE passwordPusher SET views=views+1 WHERE id=:id AND xviews > views";
			$selectQuery = "SELECT seccred, views FROM passwordPusher WHERE id=:id AND xtime > UTC_TIMESTAMP()";
			$params = array('id' => $inputToken);
			
			// First update the view count
			$result = $db->update($updateQuery, $params);
			
			// If views update fails, end immediately before printing credentials.
			if($result != 1){
				return false;
			}
			
			// Prepare and execute the retrieval query
			$data = $db->fetchAssoc($selectQuery, $params);
			
			if(isset($data->seccred) == true){
				// Erase all expired entries for good measure.
				PHPasswordPusher::eraseExpiredEntries();
				
				// Return the data
				$outputData = $data->seccred;
				return true;
			}
		}
		
		return NULL;
	}
	
	public static function removeCredentials($inputToken){
		global $db;
		if(isset($inputToken) && $inputToken !== ""){
			$query = "DELETE FROM passwordPusher WHERE id=:id";
			$params = array('id' => $inputToken);
			
			$result = $db->delete($query, $params);
			
			if($result == 1){
				return true;
			}
		}
		
		return false;
	}
	
	public static function stringInCorrectFormat($inputString){
		if(($result = preg_match("/([\w\d]{8}-[\w\d]{4}-[\w\d]{4}-[\w\d]{4}-[\w\d]{12})/", $inputString, $matches)) === 1){
			return true;
		}
		
		return false;
	}
}
?>
