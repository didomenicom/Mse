<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

switch(Url::getParts('act')){
	default:
		ImportFile(Url::getAdminDirBase() . DS . "pages/help/View.php");
		View();
		break;
}
?>