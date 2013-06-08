<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
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
	
	public function Mysql(){
		global $Config;
		
		/* Connection Informaion */
		$db_host      = $Config->getSystemVar('database_Host');
		$db_user      = $Config->getSystemVar('database_User');
		$db_name      = $Config->getSystemVar('database_Name');
		$db_pass      = $Config->getSystemVar('database_Pass');
		
		/* Connect to MySQL */
		$this->dbPointer = new PDO('mysql:dbname=' . $db_name . '; host=' . $db_host, $db_user, $db_pass) or die("Failed to connect to mysql server");
  		$this->dbPointer->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	public function query($query, $params = array()){
		if($this->dbPointer != NULL && isset($query) && $query != ""){
			try {
				$statement = $this->dbPointer->prepare($query);
				$statement->execute($params);
				return $statement->fetchAll(PDO::FETCH_CLASS);
			} catch(PDOException $e){
				Log::fatal("Mysql: query -- DB Error: '" . $e->getMessage() . "'");
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
				Log::fatal("Mysql: insert -- DB Error: '" . $e->getMessage() . "'");
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
				Log::fatal("Mysql: delete -- DB Error: '" . $e->getMessage() . "'");
			}
		}
		
		return NULL;
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
				Log::fatal("Mysql: update -- DB Error: '" . $e->getMessage() . "'");
			}
		}
		
		return -1;
	}
	
	public function getLastInsertId(){
		return $this->lastInsertId;
	}
}
?>