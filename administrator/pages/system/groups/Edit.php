<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function Edit(){
	if(UserFunctions::hasComponentAccess("groups", "edit") == true){
		ImportClass("Group.Groups");
		ImportClass("Group.Group");
		
		$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
		$id = (Url::getParts('id') > 0 ? Url::getParts('id') : 0);
		
		// Create the class
		$data = new Group($id);
		
		if($result == 0){
			// Display the page text
			echo Text::pageTitle((Url::getParts('act') === "edit" ? "Edit" : "Add") . " Permission Group");
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
				window.location.replace("<?php echo Url::getAdminHttpBase(); ?>/index.php?option=groups&act=manage");
			}
			</script>
			<div id="formMessages" class="alert alert-error hidden"></div>
			<form name="adminForm" id="adminForm" method="post" action="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=groups&act=<?php echo (Url::getParts('act') === "edit" ? "edit" . ($id > 0 ? "&id=" . $id : "") : "add"); ?>&result=1">
				<ul id="componentTab" class="nav nav-tabs">
					<li class="active"><a href="#general" data-toggle="tab">General</a></li>
					<li><a href="#permissions" data-toggle="tab">Permissions</a></li>
				</ul>
				<div id="componentTabContent" class="tab-content">
					<div class="tab-pane fade in active" id="general">
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
									// List all of the groups
									$groups = new Groups();
									while($groups->hasNext() == true){
										$row = $groups->getNext();
										
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
							<label class="control-label" for="inputActive">Active</label>
							<div class="controls">
								<select name="inputActive" id="inputActive">
									<option value="0"<?php echo ($id > 0 ? ($data->getActive() == 0 ? "selected=\"selected\"" : "") : "selected=\"selected\""); ?>>No</option>
									<option value="1"<?php echo ($id > 0 && $data->getActive() == 1 ? "selected=\"selected\"" : ""); ?>>Yes</option>
								</select>
							</div>
						</div>
					</div>
					<div class="tab-pane fade in" id="permissions">
					<div class="accordion" id="accordionPermissions">
						<?php 
						ImportClass("Component.Components");
						
						$components = new Components();
						
						if($components->rowsExist()){
							while($components->hasNext()){
								$row = $components->getNext();
								
								// Only display if there are functions defined
								if(strlen($row->getFunctions()) > 0){
									?>
									<div class="accordion-group">
										<div class="accordion-heading">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionPermissions" href="#collapse<?php echo $row->getName(); ?>">
												<?php echo $row->getDisplayName(); ?>
											</a>
										</div>
										<div id="collapse<?php echo $row->getName(); ?>" class="accordion-body collapse">
											<div class="accordion-inner">
												<?php 
												// List all of the functions
												$functions = explode("|", $row->getFunctions());
												
												foreach($functions as $function){
													preg_match("/([\w\s]+)\((\w+)\)/", $function, $matches);
													$data->getComponentFunction($row->getName(), $matches[2]);
													?>
												<div>
													<input type="checkbox"<?php echo ($id > 0 && $data->getComponentFunction($row->getName(), $matches[2]) == true ? " checked=\"checked\"" : ""); ?> name="componentFunction[<?php echo $row->getName(); ?>_<?php echo $matches[2]; ?>]" id="componentFunction[<?php echo $row->getName(); ?>_<?php echo $matches[2]; ?>]" value="1" /> <?php echo $matches[1]; ?>
												</div>
													<?php 
												}
												?>
											</div>
										</div>
									</div>
									<?php 
								}
							}
						}
						?>
						</div>
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
				$data->setParent($info['inputParent']);
				$data->setActive($info['inputActive']);
				
				// Loop through all of the component functions
				$data->clearComponentFunctions();
				
				if(isset($info['componentFunction'])){
					foreach($info['componentFunction'] as $key => $value){
						// Break the key apart
						list($component, $function) = explode("_", $key);
						
						$data->setComponentFunction($component, $function, $value);
					}
				}
				
				if($data->save() == true){
					if($id > 0){
						Messages::setMessage("Group Saved", Define::get("MessageLevelSuccess"));
					} else {
						Messages::setMessage("Group Added", Define::get("MessageLevelSuccess"));
					}
					
					Url::redirect(Url::getAdminHttpBase() . "/index.php?option=groups&act=manage", 0, false);
				}
			} else {
				Messages::setMessage($errorMessage, Define::get("MessageLevelError"));
				Url::redirect(Url::getAdminHttpBase() . "/index.php?option=groups&act=" . (Url::getParts('act') === "edit" ? "edit" . ($id > 0 ? "&id=" . $id : "") : "add"), 0, false);
			}
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(Url::home(), 3, false);
	}
}

?>
