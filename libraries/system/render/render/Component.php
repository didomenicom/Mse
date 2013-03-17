<?php
ImportClass("Render.ComponentRender");

class ComponentRenderComponent extends ComponentRender {
	
	public function render($name, $attributes, $result){ // TODO: Handle method scope
		global $config;
		
		// Check to see if there is a name
		if(isset($name) && $name != ""){
			// Name exists... figure out what it is
			// Clean up the text
			$name = strtolower($name);
			
			$content = "";
			
			// Build array of available components
			// Components are listed in pages
			$components = File::directoryContents(Url::getBasePath() . "/pages");
			
			// Loop through each
			foreach($components as $component){
				$origComponent = $component;
				if(!is_dir(Url::getBasePath() . "/pages/" . $component)){
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
						
						// At this point the file exists
						if(file_exists(Url::getBasePath() . "/pages/" . $origComponent)){
							// Build the class name
							$name = ucfirst($name);
							
							// Build the file
							ob_start();
							include_once Url::getBasePath() . "/pages/" . $origComponent; // TODO: Use file management class
							$name($attributes);
							$content = ob_get_contents();
							ob_end_clean();
						} else {
							// Unknown component
							Log::fatal("ComponentRenderComponent: render -- unknown name variable - name = '" . $name . "'");
						}
					}
				}
			}
			
			return $content;
		} else {
			// This is the main content item
			$output = "";
			
			// Check if the default file exists
			if(File::exists(Url::getBasePath() . "/pages", "home.php") == true){
				// Read in the file and execute it
				ob_start();
				include_once Url::getBasePath() . "/pages/home.php"; // TODO: Use file management class
				home();
				$output = ob_get_contents();
				ob_end_clean();
			} else {
				// Default file doesn't exist -- display message
				Log::fatal("ComponentRenderComponent: render -- could not find default file - " . Url::getBasePath() . "/pages/home.php");
				
				$output = "Default function not found. Please add the file '" . URL::getBasePath() . "/pages/home.php' with a function home in it.";
			}
			
			return $output;
		}
	}
}
?>