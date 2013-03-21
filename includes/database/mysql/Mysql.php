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
	
	public function __sleep(){
		return array();
	}
	
	public function __wakeup(){
		$this->db = getInstanceOf('Db');
	}
	
	public function Mysql(){
		global $Config;
		
		/* Connection Informaion */
		$db_host      = $Config->getVar('database_Host');
		$db_user      = $Config->getVar('database_User');
		$db_name      = $Config->getVar('database_Name');
		$db_pass      = $Config->getVar('database_Pass');
		
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
				Log::warn("Mysql: query -- DB Error: '" . $e->getMessage() . "'");
			}
		}
		
		return NULL;
	}
	
	public function insert($query, $params = array()){
		if($this->dbPointer != NULL && isset($query) && $query != ""){
			try {
				$statement = $this->dbPointer->prepare($query);
				
				return $statement->execute($params);
			} catch(PDOException $e){
				Log::warn("Mysql: insert -- DB Error: '" . $e->getMessage() . "'");
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
				Log::warn("Mysql: delete -- DB Error: '" . $e->getMessage() . "'");
			}
		}
		
		return NULL;
	}
	
	/**
	 * Returns number of rows affected
	 */
	public function update($query, $params = array()){
		if($this->dbPointer != NULL && isset($query) && $query != ""){
			try {
				$statement = $this->dbPointer->prepare($query);
				if($statement->execute($params) == true){
					return $statement->rowCount();
				}
			} catch(PDOException $e){
				Log::warn("Mysql: update -- DB Error: '" . $e->getMessage() . "'");
			}
		}
		
		return NULL;
	}
	
	public function getPtr(){
		return $this->dbPointer;
	}
}
?>