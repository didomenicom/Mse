<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function Edit(){
	if(UserFunctions::getLoggedIn() != NULL && true == true){
		ImportClass("Help.Helps");
		ImportClass("Help.Help");
		
		$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
		$id = (Url::getParts('id') > 0 ? Url::getParts('id') : 0);
		
		// Create the class
		$data = new Help($id);
		
		if($result == 0){
			// Display the page text
			echo Text::pageTitle((Url::getParts('task') === "edit" ? "Edit" : "Add") . " Help Menu");
			?>
			<script type="text/javascript">
			function checkForm(){
				if($("#inputComponent").find("option:selected").val() == 0){
					$("#formMessages").html("You need to select a component").removeClass("hidden");
					return false;
				}

				if($("#inputTitle").val() == ""){
					$("#formMessages").html("You need to enter a title").removeClass("hidden");
					return false;
				}
				
				return true;
			}
			
			function cancel(){
				window.location.replace("<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=help&task=manage");
			}
			</script>
			<div id="formMessages" class="alert alert-error hidden"></div>
			<form name="adminForm" id="adminForm" method="post" action="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=help&task=<?php echo (Url::getParts('task') === "edit" ? "edit" . ($id > 0 ? "&id=" . $id : "") : "add"); ?>&result=1">
				<div class="control-group">
					<label class="control-label" for="inputComponent">Component</label>
					<div class="controls">
						<select name="inputComponent" id="inputComponent">
							<option value="0"<?php echo ($id == 0 || $data->getComponent() == 0 ? "selected=\"selected\"" : ""); ?>>- Select Component -</option>
							<?php
							// List all of the locations
							ImportClass("Component.Components");
							$components = new Components();
							while($components->hasNext() == true){
								$row = $components->getNext();
								?>
							<option value="<?php echo $row->getId(); ?>"<?php echo ($id > 0 && $data->getComponent() == $row->getId() ? "selected=\"selected\"" : ""); ?>><?php echo $row->getDisplayName(1); ?></option>
								<?php
							}
							?>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputTitle">Title</label>
					<div class="controls">
						<input type="text" name="inputTitle" id="inputTitle" value="<?php echo ($id > 0 ? $data->getTitle() : ""); ?>" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputContent">Content</label>
					<div class="controls">
						<textarea name="inputContent" id="inputContent" rows="3"><?php echo ($id > 0 ? $data->getContent(1) : ""); ?></textarea>
						<script type="text/javascript">
						CKEDITOR.replace('inputContent');
						</script>
					</div>
				</div>
				<div class="controls">
					<div class="form-actions">
						<button type="submit" class="btn btn-primary" onClick="return checkForm();">Save changes</button>
						<button type="button" class="btn" onClick="return cancel();">Cancel</button>
					</div>
				</div>
			</form>
			<?php
		}
		
		if($result == 1){
			$info = Form::getParts(array("inputContent"));
			$error = false;
			$errorMessage = "";
			
			if(!isset($info['inputComponent']) || $info['inputComponent'] === ""){
				$error = true;
				$errorMessage = "Please select a component";
			}
			
			if(!isset($info['inputTitle']) || $info['inputTitle'] === ""){
				$error = true;
				$errorMessage = "Please enter a title";
			}
			
			if($error == false){
				// Save it
				$data->setComponent($info['inputComponent']);
				$data->setTitle($info['inputTitle']);
				$data->setContent($info['inputContent']);
				
				if($data->save() == true){
					if($id > 0){
						Messages::setMessage("Help Menu Saved", Define::get("MessageLevelSuccess"));
					} else {
						Messages::setMessage("Help Menu Added", Define::get("MessageLevelSuccess"));
					}
					
					Url::redirect(Url::getAdminHttpBase() . "/index.php?option=development&act=help&task=manage", 0, false);
				}
			} else {
				Messages::setMessage($errorMessage, Define::get("MessageLevelError"));
				Url::redirect(Url::getAdminHttpBase() . "/index.php?option=development&act=help&task=" . (Url::getParts('act') === "edit" ? "edit" . ($id > 0 ? "&id=" . $id : "") : "add"), 0, false);
			}
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(Url::home(), 3, false);
	}
}

?>
