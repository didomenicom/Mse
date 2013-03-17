<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

// Grab the encryption class
ImportClass('Encryption.Encryption');

// Setup the keys
global $Config;
Encryption::setIv($Config->getVar('encryption_iv'));
Encryption::setKey($Config->getVar('encryption_key'));

?>