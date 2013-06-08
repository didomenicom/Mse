<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

function Menu($attributes){
	$position = (isset($attributes['position']) && $attributes['position'] !== "" ? $attributes['position'] : NULL);
	
	if($position !== NULL){
		MenuGenerator::generate($position, Define::get('baseSystem'));
	}
}
?>