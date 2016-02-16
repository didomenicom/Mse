<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

function event(){
	global $db, $Config;
	echo Text::pageTitle("Mysql Database Events");
	
//	echo "TODO: list";
	
//	print_r("SHOW EVENTS FROM " . $Config->getSystemVar('database_Name'));
}

?>