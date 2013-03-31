<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

ImportClass("Render.ComponentRender");

// TODO: Cleanup (Define::get('baseSystem') == 1 ? Url::getAdminDirBase() : Url::getDirBase()) as variable
class ComponentRenderComponent extends ComponentRender {
	
	public function render($name, $attributes, $result){ // TODO: Handle method scope
		global $config;
		
		// Check to see if there is a name
		if(isset($name) && $name != ""){
			$content = "";
			// Name exists... figure out what it is
			// Clean up the text
			$name = strtolower($name);
			
			// Build array of available components
			// Components are listed in pages
			$components = File::directoryContents((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")));
			
			// Loop through each
			foreach($components as $component){
				$origComponent = $component;
				if(!is_dir((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")) . DS . $component)){
					// Check for file extension
					if(substr_count($component, '.') > 0){
						// Remove . and extension -- assuming anything after last . is exension
						$parts = explode(".", $component);
						array_pop($parts);
						$component = implode(".", $parts);
					}
					
					// put it to lowercase 
					$component = strtolower($component);
					
					// Check if it is the one we are looking for
					if($name === $component){
						// Remove name from attributes list
						if(isset($attributes['name'])){
							unset($attributes['name']);
						}
						
						// Build the class name
						$name = ucfirst($name);
						
						// Build the file
						ob_start();
						$result = ImportFile((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")) . DS . $origComponent);
						$name($attributes);
						$content = ob_get_contents();
						ob_end_clean();
					}
				}
			}
			
			return $content;
		} else {
			// This is the main content item
			$output = "";
			
			if(Url::getParts('option') != NULL){
				// This is a specific function -- Find and load it
				$option = ucfirst(strtolower(Url::getParts('option')));
				
				if(File::exists((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")), $option . ".php") == true){
					// Option exists - Read in the file and execute it
					ob_start();
					$result = ImportFile((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")) . DS . $option . ".php");
					$output = ob_get_contents();
					ob_end_clean();
				} else {
					// Option doesn't exist - Display error message
					$output = "The component you have requested doesn't appear to exist. If you have gotten this message in error, please contact an administrator"; // TODO: Add error message (red) class
				}
			} else {
				// Check if the default file exists
				if(File::exists((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")), DS . "home.php") == true){
					// Read in the file and execute it
					ob_start();
					$result = ImportFile((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")) . DS . "home.php");
					home();
					$output = ob_get_contents();
					ob_end_clean();
				} else {
					// Default file doesn't exist -- display message
					Log::fatal("ComponentRenderComponent: render -- could not find default file - " . (Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")) . DS . "home.php");
					
					$output = "Default function not found. Please add the file '" . (Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")) . DS . "home.php' with a function home in it.";
				}
			}
			
			return $output;
		}
	}
}
?>