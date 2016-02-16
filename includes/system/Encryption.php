<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

// Grab the encryption class
ImportClass('Encryption.Encryption');

// Setup the keys
global $Config;
Encryption::setIv($Config->getSystemVar('encryption_iv'));
Encryption::setKey($Config->getSystemVar('encryption_key'));

?>
