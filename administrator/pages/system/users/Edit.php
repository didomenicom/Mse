<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function Edit(){
	if(UserFunctions::hasComponentAccess("users", "edit") == true){
		ImportClass("Group.Groups");
		ImportClass("User.User");
		
		$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
		$id = (Url::getParts('id') > 0 ? Url::getParts('id') : 0);
		
		// Create the class
		$data = new User($id);
		
		if($result == 0){
			// Display the page text
			echo Text::pageTitle((Url::getParts('act') === "edit" ? "Edit" : "Add") . " User");
			?>
			<script type="text/javascript">
			function checkForm(){
				if($("#inputName").val() == ""){
					$("#formMessages").html("You need to enter a name").removeClass("hidden");
					return false;
				} else if($("#inputUsername").val() == ""){
					$("#formMessages").html("You need to enter a username").removeClass("hidden");
					return false;
				} else if($("#inputEmail").val() == ""){
					$("#formMessages").html("You need to enter a email address").removeClass("hidden");
					return false;
				} else if($("#userId").val() == 0 && $("#inputPassword").val() == ""){
					$("#formMessages").html("You need to enter a password").removeClass("hidden");
					return false;
				} else if($("#userId").val() == 0 && $("#inputConfirmPassword").val() == ""){
					$("#formMessages").html("You need to confirm the password").removeClass("hidden");
					return false;
				} else if($("#inputPassword").val() != "" && $("#inputPassword").val() != $("#inputConfirmPassword").val()){
					$("#formMessages").html("The passwords don't match").removeClass("hidden");
					return false;
				}
				
				return true;
			}
			
			function cancel(){
				window.location.replace("<?php echo Url::getAdminHttpBase(); ?>/index.php?option=users&act=manage");
			}
			</script>
			<div id="formMessages" class="alert alert-error hidden"></div>
			<form name="adminForm" id="adminForm" method="post" action="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=users&act=<?php echo (Url::getParts('act') === "edit" ? "edit" . ($id > 0 ? "&id=" . $id : "") : "add"); ?>&result=1">
				<div class="control-group">
					<label class="control-label" for="inputName">Name</label>
					<div class="controls">
						<input type="text" name="inputName" id="inputName" value="<?php echo ($id > 0 ? $data->getName(1) : ""); ?>" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputUsername">Username</label>
					<div class="controls">
						<input type="text" name="inputUsername" id="inputUsername" value="<?php echo ($id > 0 ? $data->getUsername(1) : ""); ?>" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputEmail">Email Address</label>
					<div class="controls">
						<input type="text" name="inputEmail" id="inputEmail" value="<?php echo ($id > 0 ? $data->getEmail(1) : ""); ?>" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputPassword">Password</label>
					<div class="controls">
						<input type="password" name="inputPassword" id="inputPassword" value="" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputConfirmPassword">Confirm Password</label>
					<div class="controls">
						<input type="password" name="inputConfirmPassword" id="inputConfirmPassword" value="" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputPermissionGroup">Permission Group</label>
					<div class="controls">
						<select name="inputPermissionGroup" id="inputPermissionGroup">
							<?php
							// List all of the groups
							$groups = new Groups(array("hasAccess" => true));
							while($groups->hasNext() == true){
								$row = $groups->getNext();
								?>
							<option value="<?php echo $row->getId(); ?>"<?php echo ($id > 0 && $data->getPermissionGroup() == $row->getId() ? "selected=\"selected\"" : ""); ?>><?php echo $row->getName(1); ?></option>
								<?php
							}
							?>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputReceiveEmail">Receive Emails</label>
					<div class="controls">
						<select name="inputReceiveEmail" id="inputReceiveEmail">
							<option value="0"<?php echo ($id > 0 ? ($data->getReceiveEmail() == 0 ? "selected=\"selected\"" : "") : "selected=\"selected\""); ?>>No</option>
							<option value="1"<?php echo ($id > 0 && $data->getReceiveEmail() == 1 ? "selected=\"selected\"" : ""); ?>>Yes</option>
						</select>
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
			
			if(!isset($info['inputUsername']) || $info['inputUsername'] === ""){
				$error = true;
				$errorMessage = "Please enter a username";
			}
			
			if(!isset($info['inputEmail']) || $info['inputEmail'] === ""){
				$error = true;
				$errorMessage = "Please enter a email address";
			}
			
			if($id == 0 && (!isset($info['inputPassword']) || $info['inputPassword'] === "")){
				$error = true;
				$errorMessage = "Please enter a password";
			}
			
			if($id == 0 && (!isset($info['inputConfirmPassword']) || $info['inputConfirmPassword'] === "")){
				$error = true;
				$errorMessage = "Please confirm the password";
			}
			
			if(isset($info['inputPassword']) && $info['inputPassword'] !== "" && $info['inputPassword'] !== $info['inputConfirmPassword']){
				$error = true;
				$errorMessage = "The passwords do not match";
			}
			
			if($error == false){
				// Save it
				$data->setName($info['inputName']);
				$data->setUsername($info['inputUsername']);
				$data->setEmail($info['inputEmail']);
				$data->setPassword(($info['inputPassword'] !== "" ? $info['inputPassword'] : ""));
				$data->setPermissionGroup($info['inputPermissionGroup']);
				$data->setReceiveEmail($info['inputReceiveEmail']);
				
				if($data->save() == true){
					if($id > 0){
						Messages::setMessage("User Saved", Define::get("MessageLevelSuccess"));
					} else {
						Messages::setMessage("User Added", Define::get("MessageLevelSuccess"));
					}
					
					Url::redirect(Url::getAdminHttpBase() . "/index.php?option=users&act=manage", 0, false);
				}
			} else {
				Messages::setMessage($errorMessage, Define::get("MessageLevelError"));
				Url::redirect(Url::getAdminHttpBase() . "/index.php?option=users&act=" . (Url::getParts('act') === "edit" ? "edit" . ($id > 0 ? "&id=" . $id : "") : "add"), 0, false);
			}
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(Url::home(), 3, false);
	}
}

?>
