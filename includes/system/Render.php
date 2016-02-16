<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

/**
 * This class handles all of the template/code rendering and display.
 * TODO: Change render to go backwards so stuff can be added to the header
 * 		 Add calls to add/get content for the header
 */
class Render {
	private $template_fileName = "";
	private $outputBuffer = NULL;
	private $templateFileTagMatches = NULL;
	private $templateFileTagResults = NULL;
	private $templateFileTagIndex = NULL;
	private $clearBufferFlag = false;
	private $clearBufferLocation = NULL;
	
	/**
	 * Constructor class for a new renderer. This class is not setup statically
	 */
	public function Render(){
		// Create defines for this class
		Define::add("adminTemplate", 1);
		Define::add("userTemplate", 2);
	}
	
	/**
	 * Loads a template file and renders it into a variable
	 */
	public function loadTemplateFile($filename, $templateType){
		if(isset($filename) && $filename != ""){
			$templateType = (isset($templateType) && $templateType > 0 ? $templateType : Define::get("userTemplate"));
			// TODO: cleanup (Define::get('baseSystem') == 1 ? Url::getAdminDirBase() : Url::getDirBase()) as var
			// Build the path to template
			switch($templateType){
				case Define::get("adminTemplate"):
					$filename = (Define::get('baseSystem') == 1 ? Url::getAdminDirBase("template") : Url::getDirBase("template")) . DS . $filename . DS . "template.php";
					break;
					
				case Define::get("userTemplate"):
					$filename = (Define::get('baseSystem') == 1 ? Url::getAdminDirBase("template") : Url::getDirBase("template")) . DS . $filename . DS . "template.php";
					break;
					
				default:
					Log::fatal("Render: loadTemplateFile -- Unknown template type - type = '" . $templateType . "'");
					break;
			}
			
			$contents = "";
			
			// Store the file name
			$this->template_fileName = $filename;
			
			// Build the file
			ob_start();
			
			$result = ImportFile($filename);
			$this->outputBuffer = ob_get_contents();
			ob_end_clean();
			
			return $result;
		} else {
			Log::fatal("Render: loadTemplateFile -- Filename not defined - filename = '" . $filename . "'");
		}
		
		return false;
	}
	
	/**
	 * Parses a template file and look for all of the mse tags. If a tag is found it will render it.
	 */
	public function parseTemplateFile(){
		$this->templateFileTagResults = array();
		$this->templateFileTagMatches = array();
		
		if(preg_match_all('#<mse:include\ type="([^"]+)" (.*)\/>#iU', $this->outputBuffer, $this->templateFileTagMatches)){
			// Parse it from bottom up
			$this->templateFileTagMatches[0] = array_reverse($this->templateFileTagMatches[0]);
			$this->templateFileTagMatches[1] = array_reverse($this->templateFileTagMatches[1]);
			$this->templateFileTagMatches[2] = array_reverse($this->templateFileTagMatches[2]);
			
			for($i = 0; ($i < count($this->templateFileTagMatches[1]) && $this->clearBufferFlag == false); $i++){
				$this->templateFileTagIndex = $i;
				$attribs = $this->parseAttributes($this->templateFileTagMatches[2][$i]);
				$type = $this->templateFileTagMatches[1][$i];
	
				$name = isset($attribs['name']) ? $attribs['name'] : NULL;
				$this->templateFileTagResults[$i] = $this->bufferComponent($type, $name, $attribs);
			}
		}
		
		return true;
	}
	
	/**
	 * Parses the attributes of a mse tag and breaks them out into an array for rendering. 
	 */
	private function parseAttributes($string){
		//Initialize variables
		$attr = array();
		$retarray = array();
	
		// Lets grab all the key/value pairs using a regular expression
		preg_match_all('/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $string, $attr);
		
		if(is_array($attr)){
			$numPairs = count($attr[1]);
			for($i = 0; $i < $numPairs; $i++){
				$retarray[$attr[1][$i]] = $attr[2][$i];
			}
		}
		
		return $retarray;
	}
	
	/**
	 * Renders the component
	 */
	private function bufferComponent($type, $name, $attribs = NULL){
		$outputBuffer = NULL;
		$result = NULL;
		
		if($type == NULL){
			Log::fatal("Render: bufferComponent -- type is null - type = '" . $type . "'");
			return false;
		}
		
		if(isset($_buffer[$type][$name])){
			$result = $_buffer[$type][$name];
		}
		
		if($render = $this->loadComponentRenderer($type)){
			$result = $render->render($name, $attribs, $result);
		}
		
		return $result;
	}
	
	/**
	 * Loads the component rendering class
	 */
	private function loadComponentRenderer($type){
		$type = ucfirst(strtolower($type));
		$class = "ComponentRender" . $type;
		
		if(!class_exists($class)){
			ImportClass("Render.Render." . ($type));
		}
		
		if(!class_exists($class)){
			Log::fatal("Render: loadComponentRenderer -- class not found - classname = '" . $class . "'");
			return NULL;
		}
		
		$instance = new $class($this);
		
		return $instance;
	}
	
	/**
	 * Returns all of the rendered code that has been stored in the buffer
	 */
	public function renderPage(){
		if($this->clearBufferFlag == true){
			$this->outputBuffer = $this->templateFileTagResults[$this->clearBufferLocation];
		} else {
			$this->outputBuffer = str_replace($this->templateFileTagMatches[0], $this->templateFileTagResults, $this->outputBuffer);
		}
		return $this->outputBuffer;
	}
	
	/**
	 * Adds a template setting to the Define class
	 */
	public function templateSetting($variable, $value){
		Define::add("templateSettings_" . $variable, $value);
	}
	
	/**
	 * This will clear all of the output EXCEPT for the code that this was called from. 
	 */
	public function clearOutput(){
		$this->clearBufferFlag = true;
		$this->clearBufferLocation = $this->templateFileTagIndex;
	}
	
	/**
	 * This will restore the clearOutput function.
	 */
	public function restoreOutput(){
		$this->clearBufferFlag = false;
		$this->clearBufferLocation = NULL;
	}
}

?>
