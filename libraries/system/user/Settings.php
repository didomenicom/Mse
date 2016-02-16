<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2013 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

class Settings {
	private $recordInfo = array();
	
	public function Settings($settingsString){
		if(isset($settingsString) && strlen($settingsString) > 0){
			self::parseString($settingsString);
		}
	}
	
	public function getSetting($settingName){
		if(isset($settingName) && strlen($settingName) > 0){
			if(isset($this->recordInfo[$settingName])){
				return $this->recordInfo[$settingName];
			}
		}
	}
	
	public function setSetting($settingName, $settingValue){
		if(isset($settingName) && strlen($settingName) > 0){
			$this->recordInfo[$settingName] = $settingValue;
		}
	}
	
	public function writeSettings(){
		$str = "";
		
		foreach($this->recordInfo as $key => $value){
			$str .= $key . "=" . $value . "|";
		}
		
		if(substr($str, (strlen($str) - 1), 1) === "|"){
			$str = substr($str, 0, (strlen($str) - 1));
		}
		
		return $str;
	}
	
	private function parseString($settingsString){
		if(isset($settingsString) && strlen($settingsString) > 0){
			$parts = explode("|", $settingsString);
			
			foreach($parts as $part){
				// Break it into index and value
				list($indexStr, $value) = explode("=", $part);
				
				// Break into individual indexes
				$this->recordInfo[$indexStr] = $value;
			}
		}
	}
}

?>
