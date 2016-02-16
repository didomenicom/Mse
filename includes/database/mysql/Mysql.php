<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

class Mysql {
	private $dbPointer = NULL;
	private $lastInsertId = NULL;
	
	public function __sleep(){
		return array();
	}
	
	public function __wakeup(){
		$this->db = getInstanceOf('Db');
	}
	
	public function Mysql($connectionInfo = null){
		global $Config;
		
		/* Connection Information */
		$db_host = "";
		$db_name = "";
		$db_user = "";
		$db_pass = "";
		
		if($connectionInfo != null){
			$db_host = $connectionInfo['host'];
			$db_name = $connectionInfo['name'];
			$db_user = $connectionInfo['user'];
			$db_pass = $connectionInfo['password'];
		} else {
			$db_host = $Config->getSystemVar('database_Host');
			$db_name = $Config->getSystemVar('database_Name');
			$db_user = $Config->getSystemVar('database_User');
			$db_pass = $Config->getSystemVar('database_Pass');
		}
		
		/* Connect to MySQL */
		if(strlen($db_host) > 0){
			try {
				$this->dbPointer = new PDO('mysql:dbname=' . $db_name . '; host=' . $db_host, $db_user, $db_pass);
				$this->dbPointer->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
				// Check to make sure the database tables are there
				$result = $this->query("SELECT * FROM information_schema.tables WHERE TABLE_SCHEMA='" . $db_name . "'");
				if(count($result) == 0){
					// Nothing was returned. This is an error
					Log::fatal("Mysql Connect: No tables in database. Cannot continue.");
					die();
				}
			} catch(PDOException $e){
				Log::fatal("Mysql Connect PDOException: Code: " . $e->getCode() . "\nMessage: " . htmlentities($e->getMessage()) . "\n");
				die();
			}
		} else {
			Log::error("Cannot connect to unknown host");
		}
	}
	
	public function query($query, $params = array()){
		if($this->dbPointer != NULL && isset($query) && $query != ""){
			try {
				$statement = $this->dbPointer->prepare($query);
				$statement->execute($params);
				return $statement->fetchAll(PDO::FETCH_CLASS);
			} catch(PDOException $e){
				Log::fatal("Mysql: query -- DB Query: '" . $query . "'          DB Error: '" . $e->getMessage() . "'");
			}
		}
		
		return NULL;
	}
	
	public function insert($query, $params = array()){
		if($this->dbPointer != NULL && isset($query) && $query != ""){
			try {
				$statement = $this->dbPointer->prepare($query);
				$result = $statement->execute($params);
				$this->lastInsertId = $this->dbPointer->lastInsertId();
				return $result;
			} catch(PDOException $e){
				Log::fatal("Mysql: insert -- DB Query: '" . $query . "'          DB Error: '" . $e->getMessage() . "'");
			}
		}
		
		return NULL;
	}
	
	/**
	 * Returns number of rows deleted
	 */
	public function delete($query, $params = array()){
		if($this->dbPointer != NULL && isset($query) && $query != ""){
			try {
				$statement = $this->dbPointer->prepare($query);
				if($statement->execute($params) == true){
					return $statement->rowCount();
				}
			} catch(PDOException $e){
				Log::fatal("Mysql: delete -- DB Query: '" . $query . "'          DB Error: '" . $e->getMessage() . "'");
			}
		}
		
		return NULL;
	}
	
	/**
	 * Returns true if truncate is successful
	 */
	public function truncate($query, $params = array()){
		if($this->dbPointer != NULL && isset($query) && $query != ""){
			try {
				$statement = $this->dbPointer->prepare($query);
				if($statement->execute($params) == true){
					true;
				}
			} catch(PDOException $e){
				Log::fatal("Mysql: truncate -- DB Error: '" . $e->getMessage() . "'");
			}
		}
		
		return false;
	}
	
	/**
	 * Returns number of rows affected
	 * -1 on error
	 */
	public function update($query, $params = array()){
		if($this->dbPointer != NULL && isset($query) && $query != ""){
			try {
				$statement = $this->dbPointer->prepare($query);
				if($statement->execute($params) == true){
					return $statement->rowCount();
				}
			} catch(PDOException $e){
				Log::fatal("Mysql: update -- DB Query: '" . $query . "'          DB Error: '" . $e->getMessage() . "'");
			}
		}
		
		return -1;
	}
	
	public function getLastInsertId(){
		return $this->lastInsertId;
	}
}
?>
