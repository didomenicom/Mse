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
		ImportClass("AjaxHandler.AjaxHandler");
		
		$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
		$id = (Url::getParts('id') !== "" ? Url::getParts('id') : NULL);
		
		// Create the class
		$data = new AjaxHandler($id);
		
		if($result == 0){
			// Display the page text
			echo Text::pageTitle((Url::getParts('act') === "edit" ? "Edit" : "Add") . " Ajax Handler");
			?>
			<script type="text/javascript">
			function checkForm(){
				if($("#inputName").val() == ""){
					$("#formMessages").html("You need to enter a name").removeClass("hidden");
					return false;
				} else if($("#inputClassName").val() == ""){
					$("#formMessages").html("You need to enter a class name").removeClass("hidden");
					return false;
				} else if($("#inputCallerFunction").val() == ""){
					$("#formMessages").html("You need to enter a caller function").removeClass("hidden");
					return false;
				}
				
				return true;
			}
			
			function cancel(){
				window.location.replace("<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=ajaxHandler&task=manage");
			}
			</script>
			<div id="formMessages" class="alert alert-error hidden"></div>
			<form name="adminForm" id="adminForm" method="post" action="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=ajaxHandler&task=<?php echo (Url::getParts('task') === "edit" ? "edit" . ($id > 0 ? "&id=" . $id : "") : "add"); ?>&result=1">
				<div class="control-group">
					<label class="control-label" for="inputName">Name</label>
					<div class="controls">
						<input type="text" name="inputName" id="inputName" value="<?php echo ($id > 0 ? $data->getName(1) : ""); ?>" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputClassName">Class Name</label>
					<div class="controls">
						<input type="text" name="inputClassName" id="inputClassName" value="<?php echo ($id > 0 ? $data->getClassName(1) : ""); ?>" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputCallerFunction">Caller Function</label>
					<div class="controls">
						<input type="text" name="inputCallerFunction" id="inputCallerFunction" value="<?php echo ($id > 0 ? $data->getCallerFunction(1) : ""); ?>" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputExpires">Expires</label>
					<div class="controls">
						<input type="text" name="inputExpires" id="inputExpires" value="<?php echo ($id > 0 ? $data->getExpireTimestamp(1) : ""); ?>" />
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
			
			if(!isset($info['inputName']) || $info['inputName'] === ""){
				$error = true;
				$errorMessage = "Please enter a name";
			}
			
			if(!isset($info['inputClassName']) || $info['inputClassName'] === ""){
				$error = true;
				$errorMessage = "Please enter a username";
			}
			
			if(!isset($info['inputCallerFunction']) || $info['inputCallerFunction'] === ""){
				$error = true;
				$errorMessage = "Please enter a email address";
			}
			
			if($error == false){
				// Save it
				$data->setName($info['inputName']);
				$data->setClassName($info['inputClassName']);
				$data->setCallerFunction($info['inputCallerFunction']);
				$data->setExpireTimestamp($info['inputExpires']);
				
				if($data->save() == true){
					if($id > 0){
						Messages::setMessage("Ajax Handler Saved", Define::get("MessageLevelSuccess"));
					} else {
						Messages::setMessage("Ajax Handler Added", Define::get("MessageLevelSuccess"));
					}
				} else {
					Messages::setMessage("Ajax Handler NOT Saved!", Define::get("MessageLevelError"));
				}
				
				Url::redirect(Url::getAdminHttpBase() . "/index.php?option=development&act=ajaxHandler&task=manage", 0, false);
			} else {
				Messages::setMessage($errorMessage, Define::get("MessageLevelError"));
				Url::redirect(Url::getAdminHttpBase() . "/index.php?option=development&act=ajaxHandler&task=" . (Url::getParts('task') === "edit" ? "edit" . ($id > 0 ? "&id=" . $id : "") : "add"), 0, false);
			}
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(UserFunctions::getLoginUrl(), 0, false);
	}
}

?>