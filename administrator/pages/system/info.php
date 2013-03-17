<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

function info(){
	echo Text::pageTitle("System Information");
	
	// Grab all of the stuff from phpinfo() and put it into a variable
	ob_start();
	phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES);
	$phpInfo = ob_get_contents();
	ob_end_clean();

	$phpInfo = preg_replace('#(\w),(\w)#', '\1, \2', $phpInfo);
	$phpInfo = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $phpInfo);
	
	echo $phpInfo;
}
?>