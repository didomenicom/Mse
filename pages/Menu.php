<?php

function Menu($attributes){
	$position = (isset($attributes['position']) && $attributes['position'] !== "" ? $attributes['position'] : NULL);
	
	if($position !== NULL){
		MenuGenerator::generate($position, Define::get('baseSystem'));
	}
}
?>