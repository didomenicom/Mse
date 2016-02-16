<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

/**
 * 
 */
class Export {
//	define("CSV", 1);
//	define("XLS", 2);
	
	private $type;
	private $location;
	private $fileName;
	private $rows = array();
	private $cells = array();
	
	/** 
	 * 
	 */
	public function MExport($inputType, $inputLocation, $inputName){
		self::setType($inputType);
		self::setLocation($inputLocation);
		self::setName($inputLocation);
	}
	
	/** 
	 * 
	 */ 
	// Get the export filetype
	public function getType(){
		return $this->type;
	}
	
	/** 
	 * 
	 */ 
	// Set the export filetype
	public function setType($inputType){
		if($inputType){
			$this->type = $inputType;
			return 1;
		}
		
		return 0;
	}
	
	/** 
	 * 
	 */ 
	// Get the full file location
	public function getLocation(){
		return $this->location;
	}
	
	/** 
	 * 
	 */ 
	// Set the full file location
	public function setLocation($inputLocation){
		if($inputLocation){
			$this->location = $input->location;
			return 1;
		}
		
		return 0;
	}
	
	/** 
	 * 
	 */ 
	// Get the export filename
	public function getFileName(){
		return $this->fileName;
	}
	
	/** 
	 * 
	 */ 
	// Set the export filename
	public function setFileName($inputName){
		if($inputName){
			// Check filename for extension
			// TODO: Check code
			$parts = explode(".", $inputName);
			
			if($parts[(count($parts) - 1)] == CSV && count($parts) != 0){
				unlink($parts[(count($parts) - 1)]);
			}
			
			$inputName = implode(".", $parts);
			
			$this->fileName = $inputName;
			return 1;
		}
		
		return 0;
	}
	
	/** 
	 * 
	 */ 
	// Add a row to the export file
	public function addRow($inputDataArray){
		if(is_array($inputDataArray)){
			$position = count($this->rows);
			$this->rows[$position] = $inputDataArray;
			
			return $position;
		}
		
		return -1;
	}
	
	/** 
	 * 
	 */ 
	// Remove a row from the file
	public function removeRow($inputRow){
		if($inputRow){
			if($this->rows[$inputRow]){
//				@unset($this->rows[$inputRow]);
				return 1;
			}
		}
		
		return 0;
	}
	
	/** 
	 * 
	 */ 
	// Clear all rows in the export
	public function clearRows($confirm = 0){
		if($confirm == 1){
			$this->rows = array();
		}
	}
	
	/** 
	 * 
	 */ 
	// Get the rows, return array
	public function getRows(){
		// TODO
	}
	
	/** 
	 * 
	 */ 
	// Add a cell to a temp array
	public function addCell($inputData){
		if($inputData && !is_array($inputData)){
			
		}
		
		return -1;
	}
	
	/** 
	 * 
	 */ 
	// Store the temp cell array as a row
	public function storeCells(){
		self::addRow($this->cells);
	}
	
	/** 
	 * 
	 */ 
	// Clear the temp cell array
	public function clearCells(){
		$this->cells = array();
	}
	
	/** 
	 * 
	 */ 
	// Get the cells, return array
	public function getCells(){
		// TODO
	}
	
	/** 
	 * 
	 */ 
	// Export to string
	public function toString(){
		// TODO
	}
	
	/** 
	 * 
	 */ 
	// Export to file
	public function export(){
		switch($this->type){
			case CSV:
				self::exportCSV();
				break;
			case XLS:
				self::exportXLS();
				break;
			default:
				self::exportCSV();
				break;
		}
	}
	
	/** 
	 * 
	 */ 
	private function exportCSV(){
		// TODO: Check for rows to export? 
		// TODO: Export using headers like XLS? 
		// Open the output file
		$fp = fopen($this->location . "/" . $this->fileName . ".csv", "w");
		
		for($i = 0; $i < count($this->rows); $i++){
			fputcsv($fp, $this->rows[$i]);
		}
		
		fclose($fp);
		
		return 1;
	}
	
	/** 
	 * 
	 */ 
	private function exportXLS(){
		// TODO
	}
}

?>
