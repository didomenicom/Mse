<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

class Db {
	private $dbPointer = NULL;
	
	private $queryArray = array(); // Log
	
	private $lastInsertId;
	
	public function Db(){
		global $Config;
		
		// Check to see what type of database is being used
		switch($Config->getVar('database_Type')){
			case "mysql":
				// Create a mysql database and pass all calls to it
				ImportClass("Database.Mysql.Mysql");
				$this->dbPointer = new Mysql();
				break;
			
			default:
				// TODO: Error unknown database type
				break;
		}
	}
	
	/**
	 * Does a query and returns the first item or NULL if no items returned from the query
	 */
	public function fetchAssoc($query, $params = array()){
		if($this->dbPointer != NULL){
			if(isset($query) && $query != ""){
				// Log query
				self::logQuery($query);
				
				$result = $this->dbPointer->query($query, $params);
				
				if(count($result) == 1){
					return $result[0];
				} else {
					// More than 1 item exists 
					// TODO: Throw warning
				}
			}
		}
		
		return NULL;
	}
	
	public function insert($query, $params = array()){
		if($this->dbPointer != NULL){
			if(isset($query) && $query != ""){
				// Log query
				self::logQuery($query);
				
				return $this->dbPointer->insert($query, $params);
			}
		}
		
		return NULL;
	}
	
	/**
	 * Does a database query update. Query and params are passed in 
	 * Returns number of rows affected
	 */
	public function update($query, $params = array()){
		if($this->dbPointer != NULL){
			if(isset($query) && $query != ""){
				// Log query
				self::logQuery($query);
				
				return $this->dbPointer->update($query, $params);
			}
		}
		
		return NULL;
	}
	
	/**
	 * Does a database query delete. Query and params are passed in
	 * Returns number of rows deleted
	 */
	public function delete($query, $params = array()){
		if($this->dbPointer != NULL){
			if(isset($query) && $query != ""){
				// Log query
				self::logQuery($query);
				
				return $this->dbPointer->delete($query, $params);
			}
		}
		
		return NULL;
	}
	
	public function getLastInsertId(){
		Log::fatal("DB ERROR");
		return $this->lastInsertId;
	}
	
	public function displayLog(){
		Log::fatal("DB ERROR");
		foreach($queryArray as $log){
			print($log . "<br />");
		}
	}
	
	public function tableExists($tableName){
		Log::fatal("DB ERROR");
//		$query = mysql_query("SHOW TABLES FROM " . Config::database_Name);
//		
//		while($row = mysql_fetch_row($result)){
//			if($row[0] === $tableName){
//				return true;
//			}
//		}
//		
//		return false;
	}
	
	private function logQuery($inputQuery){
		
	}
	
	public function isConnected(){
		return ($this->dbPointer == NULL ? false : true);
	}
	
	public function getPtr(){
		return $this->dbPointer->getPtr();
	}
	
	
	
	
	/**
	 * This is a stack based implementation. 
	 * The items will be freed when the fetchObjectFree is called
	 * An error message will be generated if any items remain in the stack 
	 * after all execution has been completed
	 * 
	 * Structure: 
	 * array(index) = array(pointer, objectArray, queryTextIndex)
	 */
	private $fetchObjectStack = array();
	
	/**
	 * This is a simplified implementation of the mysql_fetch_object and mysql_free functions with a stack based implementation
	 */
	public function fetchObject($query, $params = array()){
		if(self::isConnected() == true && isset($query) && $query !== ""){
			// Log query
			self::logQuery($query);
			
			$result = $this->dbPointer->query($query, $params);
			
			if(count($result) > 0){
				// There are item returned from the query -- add it to the stack
				// Create a new stack entry 
				$stackEntry = array("pointer" => 0, "objectArray" => array(), "queryTextIndex" => 0);
				
				// Add the items to the object array
				foreach($result as $row){
					array_push($stackEntry['objectArray'], $row);
				}
				
				// Add the stack entry to the fetch object stack
				array_push($this->fetchObjectStack, $stackEntry);
				
				return count($stackEntry['objectArray']);
			}
			
			return 0;
		}
		
		return NULL;
	}
	
	/**
	 * 
	 */
	public function fetchObjectHasNext(){
		$ptr = (count($this->fetchObjectStack) - 1);
		
		if($ptr >= 0){
			return ($this->fetchObjectStack[$ptr]['pointer'] >= count($this->fetchObjectStack[$ptr]['objectArray']) ? false : true);
		}
		
		return false;
	}
	
	/**
	 * 
	 */
	public function fetchObjectGetNext(){
		$ptr = (count($this->fetchObjectStack) - 1);
		
		if($ptr >= 0){
			$result = $this->fetchObjectStack[$ptr]['objectArray'][$this->fetchObjectStack[$ptr]['pointer']];
			$this->fetchObjectStack[$ptr]['pointer']++;
			
			return $result;
		}
		
		return NULL;
	}
	
	/**
	 * This will remove an item from the fetch object stack 
	 */
	public function fetchObjectDestroy(){
		array_pop($this->fetchObjectStack);
	}
}

?>