<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

// Import the different superglobals
ImportClass("Superglobal.Cookie");
ImportClass("Superglobal.File");
ImportClass("Superglobal.Get");
ImportClass("Superglobal.Post");
ImportClass("Superglobal.Server");
ImportClass("Superglobal.Session");

// TODO: Add cleanup class to put everything back into the superglobal variables
?>
