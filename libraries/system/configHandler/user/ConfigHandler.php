<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
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