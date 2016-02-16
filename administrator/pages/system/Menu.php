<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function Menu($attributes){
	$position = (isset($attributes['position']) && $attributes['position'] !== "" ? $attributes['position'] : NULL);
	
	if($position !== NULL){
		MenuGenerator::generate($position, Define::get('baseSystem'));
	}
}
?>
