<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2013 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function DeleteCheck($id){
	if(isset($id) && $id !== ""){
		// TODO: Complete
		return true;
	}
	
	return false;
}

?>
