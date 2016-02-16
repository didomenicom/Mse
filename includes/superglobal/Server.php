<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

ImportClass("SuperGlobal");

class Server extends SuperGlobal {
	public static function get($input){
		return $_SERVER[$input];
	}
}
?>
