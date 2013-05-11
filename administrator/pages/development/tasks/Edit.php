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
		ImportClass("Development.Tasks.Tasks");
		ImportClass("Development.Tasks.Task");
		
		$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
		$id = (Url::getParts('id') > 0 ? Url::getParts('id') : 0);
		
		// Create the class
		$data = new Task($id);
		
		if($result == 0){
			// Display the page text
			echo Text::pageTitle((Url::getParts('task') === "edit" ? "Edit" : "Add") . " Task");
			?>
			<script type="text/javascript">
			function checkForm(){
				if($("#inputName").val() == ""){
					$("#formMessages").html("You need to enter a name").removeClass("hidden");
					return false;
				}
				
				return true;
			}
			
			function cancel(){
				window.location.replace("<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=tasks&task=manage");
			}
			</script>
			<div id="formMessages" class="alert alert-error hidden"></div>
			<form name="adminForm" id="adminForm" method="post" action="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=tasks&task=<?php echo (Url::getParts('act') === "edit" ? "edit" . ($id > 0 ? "&id=" . $id : "") : "add"); ?>&result=1">
				<div class="control-group">
					<label class="control-label" for="inputName">Name</label>
					<div class="controls">
						<input type="text" name="inputName" id="inputName" value="<?php echo ($id > 0 ? $data->getName(1) : ""); ?>" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputDescription">Description</label>
					<div class="controls">
						<textarea name="inputDescription" id="inputDescription" rows="3"><?php echo ($id > 0 ? $data->getDescription(1) : ""); ?></textarea>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputCompleted">Completed</label>
					<div class="controls">
						<select name="inputCompleted" id="inputCompleted">
							<option value="0"<?php echo ($id > 0 ? ($data->getCompleted() == 0 ? "selected=\"selected\"" : "") : "selected=\"selected\""); ?>>No</option>
							<option value="1"<?php echo ($id > 0 && $data->getCompleted() == 1 ? "selected=\"selected\"" : ""); ?>>Yes</option>
						</select>
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
			$info = Form::getParts();
			$error = false;
			$errorMessage = "";
			
			if(!isset($info['inputName']) || $info['inputName'] === ""){
				$error = true;
				$errorMessage = "Please enter a name";
			}
			
			if($error == false){
				// Save it
				$data->setName($info['inputName']);
				$data->setDescription($info['inputDescription']);
				$data->setCompleted($info['inputCompleted']);
				
				if($data->save() == true){
					if($id > 0){
						Messages::setMessage("Task Saved", Define::get("MessageLevelSuccess"));
					} else {
						Messages::setMessage("Task Added", Define::get("MessageLevelSuccess"));
					}
					
					Url::redirect(Url::getAdminHttpBase() . "/index.php?option=development&act=tasks&task=manage", 0, false);
				}
			} else {
				Messages::setMessage($errorMessage, Define::get("MessageLevelError"));
				Url::redirect(Url::getAdminHttpBase() . "/index.php?option=development&act=tasks&task=" . (Url::getParts('act') === "edit" ? "edit" . ($id > 0 ? "&id=" . $id : "") : "add"), 0, false);
			}
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(UserFunctions::getLoginUrl(), 0, false);
	}
}

?>