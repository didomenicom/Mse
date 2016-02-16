<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

class ShortLink {
	/**
	 * This function will execute and redirect based on the inputted short string
	 * On success it returns true
	 * Otherwise, false
	 */
	public static function execute($shortString){
		if(strlen($shortString) > 0){
			$record = ShortLink::get($shortString);
			
			if($record != NULL){
				// Check if it is active
				if(isset($record->active) && $record->active == true){
					// Check if it is expired
					if($record->expirationDate > 0 && Date::getCurrentTimeStamp() > strtotime($record->expirationDate)){
						return false;
					}
					
					// The checks are done now lets redirect
					if(isset($record->redirectLink) && strlen($record->redirectLink) > 0){
						ShortLink::updateHitCount($shortString);
						
						// Redirect it
						header("HTTP/1.1 301 Moved Permanently");
						header("Location: http://" . Text::trimBeginningOfString($record->redirectLink, "http://")); // TODO: Handle internal links better
						
						return true;
					}
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Returns the data from the db 
	 * If short link exists returns the array struct
	 * Otherwise, returns NULL
	 */
	public static function get($shortString){
		global $db;
		
		if(strlen($shortString) > 0){
			$info = $db->fetchAssoc("SELECT * FROM shortLink WHERE id='" . $shortString . "'");
			
			if(isset($info->id) && $info->id === $shortString){
				return $info;
			}
		}
		
		return NULL;
	}
	
	/**
	 * Adds a new short link to the database
	 * Returns true if the short link is created
	 * Otherwise false
	 */
	public static function add($redirectLink, $internal = false, $active = true, $expirationDate = "0000-00-00 00:00:00"){
		global $db;
		
		if(strlen($redirectLink) > 0){
			ImportClass("Miscellaneous.Miscellaneous");
			
			// Create a short link
			$shortLink = Miscellaneous::generateRandomString(6);
			// Check if it exists
			while(ShortLink::get($shortLink) != NULL){
				// This exists... 
				$shortLink = Miscellaneous::generateRandomString(6);
			}
			
			// We finally have one that doesn't exist... store it
			$result = $db->insert("INSERT INTO shortLink (id, redirectLink, internal, active, expirationDate) VALUES (" .
					"'" . $shortLink . "', " .
					"'" . $redirectLink . "', " .
					"'" . $internal . "', " .
					"'" . $active . "', " .
					"'" . $expirationDate . "')");
			
			if($result == true){
				Log::action("Short Link (" . $shortLink . ") added");
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Updates an existing short link
	 * Returns true if the short link is dated
	 * Otherwise false
	 */
	public static function update($shortString, $redirectLink, $internal = false, $active = true, $expirationDate = "0000-00-00 00:00:00"){
		global $db;
	
		if(strlen($shortString) > 0 && strlen($redirectLink) > 0){
			$result = $db->update("UPDATE shortLink SET " .
					"redirectLink='" . $redirectLink . "', " .
					"internal='" . $internal . "', " .
					"active='" . $active . "', " .
					"expirationDate='" . $expirationDate . "' " .
					"WHERE id='" . $shortString . "'");
			
			if($result == true){
				Log::action("Short Link (" . $shortString . ") edited");
				
				return true;
			}
		}
	
		return false;
	}
	
	/**
	 * Deletes a short link from the database
	 * Returns true on success
	 * Otherwise false
	 */
	public static function delete($shortString){
		global $db;
		
		if(strlen($shortString) > 0){
			// We finally have one that doesn't exist... store it
			$result = $db->delete("DELETE FROM shortLink WHERE id='" . $shortString . "'");
				
			if($result == true){
				Log::action("Short Link (" . $shortString . ") deleted");
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Updates the hit count for a short link 
	 * On success returns true
	 * Otherwise false
	 */
	public static function updateHitCount($shortString){
		global $db;
		
		if(strlen($shortString) > 0){
			$result = $db->update("UPDATE shortLink SET hitCount=hitCount + 1 WHERE id='" . $shortString . "'");
			
			if($result == true){
				return true;
			}
		}
		
		return false;
	}
}

?>