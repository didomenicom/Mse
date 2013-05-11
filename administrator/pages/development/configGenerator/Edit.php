<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

function Edit(){
	if(UserFunctions::getLoggedIn() != NULL && true == true){
		ImportClass("Config.ConfigOption");
		
		$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
		$id = (Url::getParts('id') !== "" ? Url::getParts('id') : NULL);
		
		// Create the class
		$data = new ConfigOption($id);
		
		if($result == 0){
			// Display the page text
			echo Text::pageTitle((Url::getParts('act') === "edit" ? "Edit" : "Add") . " Configuration");
			?>
			<script type="text/javascript">
			function checkForm(){
				if($("#inputComponent").val() == ""){
					$("#formMessages").html("You need to enter a comonent").removeClass("hidden");
					return false;
				} else if($("#inputType").val() == ""){
					$("#formMessages").html("You need to select a type").removeClass("hidden");
					return false;
				} else if($("#inputName").val() == ""){
					$("#formMessages").html("You need to enter a name").removeClass("hidden");
					return false;
				}
				
				return true;
			}
			
			function cancel(){
				window.location.replace("<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=configGenerator&task=manage");
			}
			</script>
			<div id="formMessages" class="alert alert-error hidden"></div>
			<form name="adminForm" id="adminForm" method="post" action="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=configGenerator&task=<?php echo (Url::getParts('task') === "edit" ? "edit" . ($id > 0 ? "&id=" . $id : "") : "add"); ?>&result=1">
				<div class="control-group">
					<label class="control-label" for="inputComponent">Component</label>
					<div class="controls">
						<input type="text" name="inputComponent" id="inputComponent" value="<?php echo ($id > 0 ? $data->getComponent(1) : ""); ?>" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputType">Type</label>
					<div class="controls">
						<select name="inputType" id="inputType">
							<option value=""<?php echo ($id > 0 ? ($data->getType() == 0 ? " selected=\"selected\"" : "") : " selected=\"selected\""); ?>>- Select -</option>
							<?php
							ImportClass("Config.ConfigTypes");
							
							if(ConfigTypes::rowsExist()){
								
								while(ConfigTypes::hasNext()){
									$row = ConfigTypes::getNext();
							?>
							<option value="<?php echo $row['id']; ?>"<?php echo ($id > 0 ? ($data->getType() == $row['id'] ? " selected=\"selected\"" : "") : ""); ?>><?php echo $row['name']?></option>
							<?php 
								}
							}
							?>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputName">Name</label>
					<div class="controls">
						<input type="text" name="inputName" id="inputName" value="<?php echo ($id > 0 ? $data->getName(1) : ""); ?>" />
					</div>
				</div>
				<div class="controls">
					<div class="form-actions">
						<button type="submit" class="btn btn-primary" onClick="return checkForm();">Save changes</button>
						<button type="button" class="btn" onClick="return cancel();">Cancel</button>
					</div>
				</div>
				<input type="hidden" name="userId" id="userId" value="<?php echo $id; ?>" />
			</form>
			<?php
		}
		
		if($result == 1){
			$info = Form::getParts();
			$error = false;
			$errorMessage = "";
			
			if(!isset($info['inputComponent']) || $info['inputComponent'] === ""){
				$error = true;
				$errorMessage = "Please enter a component";
			}
			
			if(!isset($info['inputType']) || $info['inputType'] === ""){
				$error = true;
				$errorMessage = "Please select a type";
			}
			
			if(!isset($info['inputName']) || $info['inputName'] === ""){
				$error = true;
				$errorMessage = "Please enter a name";
			}
			
			if($error == false){
				// Save it
				$data->setComponent($info['inputComponent']);
				$data->setType($info['inputType']);
				$data->setName($info['inputName']);
				
				if($data->save() == true){
					if($id > 0){
						Messages::setMessage("Config Saved", Define::get("MessageLevelSuccess"));
					} else {
						Messages::setMessage("Config Added", Define::get("MessageLevelSuccess"));
					}
				} else {
					Messages::setMessage("Config NOT Saved!", Define::get("MessageLevelError"));
				}
				
				Url::redirect(Url::getAdminHttpBase() . "/index.php?option=development&act=configGenerator&task=manage", 0, false);
			} else {
				Messages::setMessage($errorMessage, Define::get("MessageLevelError"));
				Url::redirect(Url::getAdminHttpBase() . "/index.php?option=development&act=configGenerator&task=" . (Url::getParts('task') === "edit" ? "edit" . ($id > 0 ? "&id=" . $id : "") : "add"), 0, false);
			}
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(UserFunctions::getLoginUrl(), 0, false);
	}
}

?>