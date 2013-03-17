<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

// Add the defines for each error level (based on Twitter bootstrap)
Define::add("MessageLevelError", 1);
Define::add("MessageLevelWarning", 2);
Define::add("MessageLevelSuccess", 3);
Define::add("MessageLevelInfo", 4);

class Messages {
	private static $message = array();
	
	/** 
	 * Adds a message to the message session
	 */
	public static function setMessage($inputMessage, $level = 4){
		if(isset($inputMessage)){
			if(!isset($_SESSION['message'])){
				$_SESSION['message'] = array();
			}
			
			array_push($_SESSION['message'], array("level" => $level, "message" => $inputMessage));
		}
	}
	
	/** 
	 * Grabs a message from the session and returns it. 
	 * Notes it completely removes it from the array
	 * TODO: Modify so it doesn't completely remove
	 */
	public static function getMessage(){
		if(self::hasMessage() == true){
			$parts = array_pop($_SESSION['message']);
			
			return $parts;
		}
		
		return NULL;
	}
	
	/** 
	 * Checks if there are any messages
	 */
	public static function hasMessage(){
		return (isset($_SESSION['message']) ? (count($_SESSION['message']) > 0 ? true : false) : false);
	}
	
	/** 
	 * Returns an array of all of the messages
	 */
	public static function getMessages(){
		$output = array();
		while(self::hasMessage() == true){
			array_push($output, self::getMessage());
		}
		
		return $output;
	}
	
	public static function displayMessages(){
		// Get the messages
		$messageArray = self::getMessages();
		
		// Check for messages
		if(count($messageArray) > 0){
			$errorFlag = false;
			// There are messages go through and print them
			
			// Check if there is an error - overrides all others
			foreach($messageArray as $message){
				if($message['level'] == Define::get("MessageLevelError")){
					$errorFlag = true;
					break;
				}
			}
			
			if($errorFlag == true){
				// There is an error grab them and display
				$text = "";
				
				foreach($messageArray as $message){
					if($message['level'] == Define::get("MessageLevelError")){
						$text .= (isset($message['bold']) && $message['bold'] == true ? "<strong>" . $message['message'] . "</strong>" : $message['message']) . "<br />";
					}
				}
				
				if($text !== ""){
					?>
					<div class="alert alert-error">
						<?php echo $text; ?>
					</div>
					<?php
				}
			} else {
				// There are no errors so group everything by the level type
				$text = array();
				
				foreach($messageArray as $message){
					// If it is not created yet
					if(!isset($text[$message['level']])){
						$text[$message['level']] = "";
					}
					
					$text[$message['level']] .= (isset($message['bold']) && $message['bold'] == true ? "<strong>" . $message['message'] . "</strong>" : $message['message']) . "<br />";
				}
				
				foreach($text as $type => $txt){
					switch($type){
						case Define::get("MessageLevelError"):
							?>
							<div class="alert alert-error">
								<?php echo $txt; ?>
							</div>
							<?php
							break;
						case Define::get("MessageLevelWarning"):
							?>
							<div class="alert alert-block">
								<?php echo $txt; ?>
							</div>
							<?php
							break;
						case Define::get("MessageLevelSuccess"):
							?>
							<div class="alert alert-success">
								<?php echo $txt; ?>
							</div>
							<?php
							break;
						case Define::get("MessageLevelInfo"):
							?>
							<div class="alert alert-info">
								<?php echo $txt; ?>
							</div>
							<?php
							break;
						default:
							// TODO: Unknown error
							break;
					}
				}
			}
		}
	}
}
?>