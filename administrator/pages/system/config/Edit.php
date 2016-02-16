<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function Edit(){
	if(UserFunctions::hasComponentAccess("config", "edit") == true){
		$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
		$com = (Url::getParts('com') !== "" ? Url::getParts('com') : NULL);
		
		if($com != NULL){
			ImportClass("Component.Component");
			ImportClass("Config.ConfigOptions");
			
			// Check if the component exists
			if(Component::exists($com) == true){
				// Create the component class
				$component = new Component($com);
				
				// Get all of the options
				$filter['component'] = $component->getId();
				$configItems = new ConfigOptions($filter);
				
				if($result == 0){
					// Get the files
					ImportFile(Url::getAdminDirBase() . DS . "pages/system/config/EditInteger.php");
					ImportFile(Url::getAdminDirBase() . DS . "pages/system/config/EditDouble.php");
					ImportFile(Url::getAdminDirBase() . DS . "pages/system/config/EditArray.php");
					ImportFile(Url::getAdminDirBase() . DS . "pages/system/config/EditTextBox.php");
					ImportFile(Url::getAdminDirBase() . DS . "pages/system/config/EditTextArea.php");
					ImportFile(Url::getAdminDirBase() . DS . "pages/system/config/EditWYSIWYG.php");
					ImportFile(Url::getAdminDirBase() . DS . "pages/system/config/EditDate.php");
					ImportFile(Url::getAdminDirBase() . DS . "pages/system/config/EditOption.php");
					ImportFile(Url::getAdminDirBase() . DS . "pages/system/config/EditTrueFalse.php");
					
					// Display the page text
					echo Text::pageTitle($component->getDisplayName(1) . " Configuration");
					
					$arrayIds = array();
					
					// Add the headers
					EditIntegerHeader();
					EditDoubleHeader();
					EditArrayHeader();
					EditTextBoxHeader();
					EditTextAreaHeader();
					EditDateHeader();
					EditOptionHeader();
					EditTrueFalseHeader();
					EditWYSIWYGHeader();
					?>
					<script type="text/javascript">
					function checkForm(){
						return true;
					}
					</script>
					
					<script type="text/javascript">
					function cancel(){
						window.location.replace("<?php echo Url::getAdminHttpBase(); ?>/index.php?option=config&act=manage"); // TODO: Set the cancel button based on the component parameter 
					}
					</script>
					<div id="formMessages" class="alert alert-error hidden"></div>
					<form name="adminForm" id="adminForm" method="post" action="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=config&act=edit&com=<?php echo $com; ?>&result=1">
						<?php 
						// Display all of the config options
						if($configItems->rowsExist()){
							while($configItems->hasNext()){
								$row = $configItems->getNext();
								
								?>
						<div class="control-group">
							<label class="control-label<?php echo (strlen($row->getComment()) > 0 ? " toolTipClass" : ""); ?>" for="inputConfigOption[<?php echo $row->getId(); ?>]" title="<?php echo (strlen($row->getComment()) > 0 ? $row->getComment() : ""); ?>"><?php echo $row->getName(); ?></label>
							<div class="controls">
								<?php 
								if($row->getType() == 1){ // Integer
									// Add the body
									EditIntegerContent($row);
								}
								
								if($row->getType() == 2){ // Double
									// Add the body
									EditDoubleContent($row);
								}
								
								if($row->getType() == 3){ // Array
									// Add the body
									EditArrayContent($row);
									
									array_push($arrayIds, $row->getId());
								}
								
								if($row->getType() == 4){ // Text Box
									// Add the body
									EditTextBoxContent($row);
								}
								
								if($row->getType() == 5){ // Text Area
									// Add the body
									EditTextAreaContent($row);
								}
								
								if($row->getType() == 6){ // Date
									// Add the body
									EditDateContent($row);
								}
								
								if($row->getType() == 7){ // Option
									// Add the body
									EditOptionContent($row);
									
									array_push($arrayIds, $row->getId());
								}
								
								if($row->getType() == 8){ // True/False
									// Add the body
									EditTrueFalseContent($row);
								}
								
								if($row->getType() == 9){ // WYSIWYG
									// Add the body
									EditWYSIWYGContent($row);
								}
								?>
							</div>
						</div>
								<?php 
							}
						}
						
						?>
						<div class="controls">
							<div class="form-actions">
								<button type="submit" class="btn btn-primary" onClick="return checkForm();">Save changes</button>
								<button type="button" class="btn" onClick="return cancel();">Cancel</button>
							</div>
						</div>
						<script type="text/javascript">
						$(document).ready(function(){ 
							// Enable the tooltips on the tooltipClass class 
							$(".tooltipClass").tooltip();
						});
						
						<?php
						if(count($arrayIds) > 0){
							foreach($arrayIds as $id){
							?>
						generateArrayTypeDisplay($("#arrayTableHeader_<?php echo $id; ?>"), $('input[name="inputConfigOption[<?php echo $id; ?>]"]'), $("#arrayTableDisplay_<?php echo $id; ?>"));
							<?php
							}
						}
						?>
						</script>
					</form>
					<?php
				}
				
				if($result == 1){
					$info = Form::getParts();
					$error = false;
					
					foreach($info['inputConfigOption'] as $key => $value){
						// Check if the key exists
						if(ConfigOption::exists($component->getId(), $key) == true){
							$cfgOption = new ConfigOption($key);
							
							// TODO: This is a temporary workaround to magic_quotes_gpc enabled
							if(ini_get('magic_quotes_gpc') === "1"){
								$cfgOption->setValue(stripslashes($value));
							} else {
								$cfgOption->setValue($value);
							}
							
							if($cfgOption->save() == false){
								$error = true;
							}
						}
					}
					
					if($error == false){
						Messages::setMessage("Configuration Saved.", Define::get("MessageLevelSuccess"));
					} else {
						Messages::setMessage("There was an error saving the configuration", Define::get("MessageLevelError"));
					}
					
					Url::redirect(Url::getAdminHttpBase() . "/index.php?option=config&act=edit&com=" . $com, 0, false);
				}
			} else {
				// Component Doesn't Exist
				Messages::setMessage("The component doesn't exist", Define::get("MessageLevelError"));
				Url::redirect(Url::getAdminHttpBase() . "/index.php", 0, false);
			}
		} else {
			// No component specified
			Messages::setMessage("An unknown error has occurred", Define::get("MessageLevelError"));
			Url::redirect(Url::getAdminHttpBase() . "/index.php", 0, false);
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(Url::home(), 3, false);
	}
}

?>
