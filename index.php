<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
define("Access", 1);

session_start();

// Base File Path
define('BASEPATH', dirname(__FILE__));

// System
require_once(BASEPATH . '/includes/system/defines.php');
require_once(BASEPATH.INCLUDES . '/system/System.php');

// Render
$Render = new Render();

// Handle template
if($Render->loadTemplateFile($Config->getSystemVar('userTemplate'), Define::get('userTemplate')) == true){
	// Template loaded successfully
	
	// Parse the template file
	if($Render->parseTemplateFile() == true){
		// Add settings
		$Render->templateSetting('jquery', true);
		
		// Render it
		echo $Render->renderPage();
	} else {
		Log::fatal("Index: parseTemplateFile -- Error");
	}
} else {
	Log::fatal("Index: loadTemplateFile -- Error");
}

?>