<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function Edit(){
	if(UserFunctions::hasComponentAccess("menus", "edit") == true){
		ImportClass("Group.Groups");
		ImportClass("Menu.Menus");
		ImportClass("Menu.MenuPositions");
		ImportClass("Menu.Menu");
		
		$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
		$id = (Url::getParts('id') > 0 ? Url::getParts('id') : 0);
		
		// Create the class
		$data = new Menu($id);
		
		if($result == 0){
			// Display the page text
			echo Text::pageTitle((Url::getParts('act') === "edit" ? "Edit" : "Add") . " Menu");
			?>
			<script type="text/javascript">
			function checkForm(){
				if($("#inputName").val() == ""){
					$("#formMessages").html("You need to enter a name").removeClass("hidden");
					return false;
				}
				
				if($("#inputPermissionGroup :selected").length == 0){
					$("#formMessages").html("You need to select a permission group").removeClass("hidden");
					return false;
				}
				
				return true;
			}
			
			function cancel(){
				window.location.replace("<?php echo Url::getAdminHttpBase(); ?>/index.php?option=menus&act=manage");
			}
			</script>
			<div id="formMessages" class="alert alert-error hidden"></div>
			<form name="adminForm" id="adminForm" method="post" action="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=menus&act=<?php echo (Url::getParts('act') === "edit" ? "edit" . ($id > 0 ? "&id=" . $id : "") : "add"); ?>&result=1">
				<div class="control-group">
					<label class="control-label" for="inputName">Name</label>
					<div class="controls">
						<input type="text" name="inputName" id="inputName" value="<?php echo ($id > 0 ? $data->getName(1) : ""); ?>" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputParent">Parent</label>
					<div class="controls">
						<select name="inputParent" id="inputParent">
							<option value="0"<?php echo ($id > 0 ? ($data->getParent() == 0 ? "selected=\"selected\"" : "") : "selected=\"selected\""); ?>>None</option>
							<?php
							// List all of the parents
							$menus = new Menus();
							while($menus->hasNext() == true){
								$row = $menus->getNext();
								
								if($id == 0 || $id > 0 && $id != $row->getId()){
									?>
							<option value="<?php echo $row->getId(); ?>"<?php echo ($id > 0 && $data->getParent() == $row->getId() ? "selected=\"selected\"" : ""); ?>><?php echo $row->getName(1); ?></option>
									<?php
								}
							}
							?>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputPosition">Position</label>
					<div class="controls">
						<select name="inputPosition" id="inputPosition">
							<?php
							// List all of the positions
							$menuPositions = new MenuPositions();
							while($menuPositions->hasNext() == true){
								$row = $menuPositions->getNext();
								?>
							<option value="<?php echo $row->getId(); ?>"<?php echo ($id > 0 && $data->getPosition() == $row->getId() ? "selected=\"selected\"" : ""); ?>><?php echo ($row->getBackend() == true ? "Backend - " : "Frontend - ") . $row->getName(1); ?></option>
								<?php
							}
							?>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputPermissionGroup">Permission Group</label>
					<div class="controls">
						<?php 
						$permissionGroupParts = explode("|", ($id > 0 ? $data->getPermissionGroup() : ""));
						?>
						<select name="inputPermissionGroup[]" id="inputPermissionGroup" multiple="multiple">
							<option value="-1"<?php echo ($id > 0 && in_array("-1", $permissionGroupParts) == true ? "selected=\"selected\"" : ""); ?>>Guest</option>
							<?php
							// List all of the groups
							$groups = new Groups(array("hasAccess" => true));
							while($groups->hasNext() == true){
								$row = $groups->getNext();
								?>
							<option value="<?php echo $row->getId(); ?>"<?php echo ($id > 0 && in_array($row->getId(), $permissionGroupParts) == true ? "selected=\"selected\"" : ""); ?>><?php echo $row->getName(1); ?></option>
								<?php
							}
							?>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputUrl">URL</label>
					<div class="controls">
						<input type="text" name="inputUrl" id="inputUrl" value="<?php echo ($id > 0 ? $data->getUrl(1) : ""); ?>" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputInternal">Internal</label>
					<div class="controls">
						<select name="inputInternal" id="inputInternal">
							<option value="1"<?php echo ($id > 0 && $data->getInternal() == 1 ? "selected=\"selected\"" : "selected=\"selected\""); ?>>Yes</option>
							<option value="0"<?php echo ($id > 0 ? ($data->getInternal() == 0 ? "selected=\"selected\"" : "") : ""); ?>>No</option>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputOrdering">Ordering</label>
					<div class="controls">
						<input type="text" name="inputOrdering" id="inputOrdering" value="<?php echo ($id > 0 ? $data->getOrdering(1) : ""); ?>" />
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
			
			if($error == false){
				// Save it
				$data->setName($info['inputName']);
				$data->setParent($info['inputParent']);
				$data->setPosition($info['inputPosition']);
				$data->setOrdering($info['inputOrdering']);
				$data->setPermissionGroup((isset($info['inputPermissionGroup']) ? $info['inputPermissionGroup'] : NULL));
				$data->setInternal($info['inputInternal']);
				$data->setUrl($info['inputUrl']);
				
				if($data->save() == true){
					if($id > 0){
						Messages::setMessage("Menu Saved", Define::get("MessageLevelSuccess"));
					} else {
						Messages::setMessage("Menu Added", Define::get("MessageLevelSuccess"));
					}
					
					Url::redirect(Url::getAdminHttpBase() . "/index.php?option=menus&act=manage", 0, false);
				}
			} else {
				Messages::setMessage($errorMessage, Define::get("MessageLevelError"));
				Url::redirect(Url::getAdminHttpBase() . "/index.php?option=menus&act=" . (Url::getParts('act') === "edit" ? "edit" . ($id > 0 ? "&id=" . $id : "") : "add"), 0, false);
			}
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(Url::home(), 3, false);
	}
}

?>
