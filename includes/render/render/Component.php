<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
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
			// Components are listed in pages/user and pages/system
			
			// Look through user
			$components = File::directoryContents((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")) . "/user");
			
			// Loop through each user
			foreach($components as $component){
				$origComponent = $component;
				if(!is_dir((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")) . DS . "user/" . $component)){
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
						$result = ImportFile((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")) . DS . "user/" . $origComponent);
						$name($attributes);
						$content = ob_get_contents();
						ob_end_clean();
					}
				}
			}
			
			// Look through system
			$components = File::directoryContents((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")) . "/system");
			
			// Loop through each system
			foreach($components as $component){
				$origComponent = $component;
				if(!is_dir((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")) . DS . "system/" . $component)){
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
						$result = ImportFile((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")) . DS . "system/" . $origComponent);
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
				
				// Check if the file exists in user
				if(File::exists((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")), "user/" . $option . ".php") == true){
					// Option exists - Read in the file and execute it
					ob_start();
					$result = ImportFile((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")) . DS . "user/" . $option . ".php");
					$output = ob_get_contents();
					ob_end_clean();
				} else 
				// Check if the file exists in system
				if(File::exists((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")), "system/" . $option . ".php") == true){
					// Option exists - Read in the file and execute it
					ob_start();
					$result = ImportFile((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")) . DS . "system/" . $option . ".php");
					$output = ob_get_contents();
					ob_end_clean();
				} else {
					// Check if the file exists and is not case sensitive
					$directory = NULL;
					if($directoryHandle = opendir((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")).DS . "user/")){
						while(false !== ($directoryEntry = readdir($directoryHandle))){
							if(strtolower($directoryEntry) === strtolower($option . ".php")){
								$directory = (Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")).DS . "user/" . $directoryEntry;
							}
						}
					}
					
					if($directory != NULL){
						// Option exists - Read in the file and execute it
						ob_start();
						$result = ImportFile($directory);
						$output = ob_get_contents();
						ob_end_clean();
					} else {
						// Option doesn't exist - Display error message
						$output = "The component you have requested doesn't appear to exist. If you have gotten this message in error, please contact an administrator"; // TODO: Add error message (red) class
					}
				}
			} else {
				// Check if the default file exists in user
				if(File::exists((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")), DS . "user/home.php") == true){
					// Read in the file and execute it
					ob_start();
					$result = ImportFile((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")) . DS . "user/home.php");
					home();
					$output = ob_get_contents();
					ob_end_clean();
				} else
				// Check if the default file exists in system
				if(File::exists((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")), DS . "system/home.php") == true){
					// Read in the file and execute it
					ob_start();
					$result = ImportFile((Define::get('baseSystem') == 1 ? Url::getAdminDirBase("pages") : Url::getDirBase("pages")) . DS . "system/home.php");
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
