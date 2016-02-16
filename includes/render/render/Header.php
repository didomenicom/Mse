<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
ImportClass("Render.ComponentRender");

class ComponentRenderHeader extends ComponentRender {
	public function render($name, $attribs, $result){
		global $Config;
		
		// Check to see if there is a name
		if(isset($name) && $name != ""){
			// Name exists... figure out what it is
			// Clean up the text
			$name = strtolower($name);
			
			switch($name){
				case "title":
					// This is the site title... grab from global config
					return $Config->getSystemVar('siteTitle');
					break;
				default:
					Log::fatal("ComponentRenderHeader: render -- unknown name variable - name = '" . $name . "'");
					break;
			}
		} else {
			// No name assume it is display all header stuff
			// TODO: 
		}
	}
}
?>
