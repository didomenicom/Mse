<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
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