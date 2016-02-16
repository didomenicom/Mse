<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

ImportClass("ClassesLibrary");

class MediaList extends ClassesLibrary {
	private $recordIndex = 0;
	private $recordQueryArray = array();
	private $filter;
	private $rowCount = 0;
	private $totalRows = 0;
	
	public function MediaList($filter = array()){
		// Figure out the default path
		global $Config;
		
		$sourceAccessDenied = ($Config->getSystemVar("fileHandling_sourceAccess") === "false" ? true : false);
		
		if(($defaultPath = $Config->getSystemVar("fileHandling_defaultDirectory")) != NULL){
			$defaultPath = Url::getDirBase() . $defaultPath;
			$directoryArray = File::directoryContents($defaultPath);
			
			if(is_array($directoryArray)){
				foreach($directoryArray as $contentItem){
					if($sourceAccessDenied == false || ($sourceAccessDenied == true && ($defaultPath !== (Url::getDirBase() . "/")))){
						$this->recordQueryArray[$this->rowCount] = $defaultPath . "/" . $contentItem;
						$this->rowCount++;
						$this->totalRows++;
					}
				}
			}
		}
	}
	
	public function hasNext(){
		return ($this->recordIndex < $this->rowCount ? true : false);
	}
	
	public function getNext(){
		$result = $this->recordQueryArray[$this->recordIndex];
		$this->recordIndex++;
		return $result;
	}
	
	public function rowsExist(){
		return ($this->rowCount > 0);
	}
	
	public function getRowCount(){
		return $this->rowCount;
	}
	
	public function getTotalRows(){
		return $this->totalRows;
	}
}
?>