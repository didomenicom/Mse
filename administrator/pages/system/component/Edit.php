<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function Edit(){
	if(UserFunctions::hasComponentAccess("component", "edit") == true){
		ImportClass("Component.Component");
		
		$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
		$id = (strlen(Url::getParts('id')) > 0 ? Url::getParts('id') : "");
		
		// Create the class
		$data = new Component($id);
		
		if(strlen($id) > 0 && $data->getSystemFunction() == false){
			if($result == 0){
				// Display the page text
				echo Text::pageTitle((Url::getParts('act') === "edit" ? "Edit" : "Add") . " Component");
				?>
				<script type="text/javascript">
				function generateFunctionsDisplay(inputString){
					// Break apart on the | 
					if(inputString != null){
						var parts = inputString.split("|");
						var outputStr = '' + 
						'<table class=\"table table-bordered table-striped table-hover">' + 
							'<thead>' + 
								'<tr>' + 
									'<th>' + 
										'Name' + 
									'</th>' + 
									'<th>' + 
										'Call' + 
									'</th>' + 
									'<th width="5%">' + 
									'</th>' + 
								'</tr>' + 
							'</thead>' + 
							'<tbody>';
						
						for(var i = 0; i < parts.length; i++){
							var row = parts[i];
							
							if(row.length > 0){
								// Break apart the row 
								var match = row.match(/([\w\s]+)\((\w+)\)/);
								var type = "";
								
								outputStr += '' + 
								'<tr>' + 
								'<td>' + 
									match[1] + 
								'</td>' + 
								'<td>' + 
									match[2] + 
								'</td>' + 
								'<td>' + 
									'<div class="pull-left"><i id="functionsArray_' + i + '" class="icon-pencil"></i></div>' + 
									'<div class="pull-right"><i id="functionsArray_' + i + '" class="icon-remove"></i></div>' + 
								'</td>' + 
								'</tr>';
							}
						}
						
						outputStr += '' + 
							'</tbody>' + 
						'</table>';
						
						$("#functionsDisplay").html(outputStr);
					}
				}
				
				$(document).ready(function(){
					$("#addOptionItem").click(function(e){
						// Format: FunctionDisplayName(functionCall) 
						var functionName = $('input[name="inputFunctions[name]"]').val();
						var functionCall = $('input[name="inputFunctions[call]"]').val();
						
						if(functionName.length == 0){
							
						} else
							
						if(functionCall.length == 0){
	
						} else {
							// All good so add to the field 
							var str = functionName + "(" + functionCall + ")";
							if($('input[name="inputFunctions[value]"]').val().length == 0 || $('input[name="inputFunctions[value]"]').val().substr(($('input[name="inputFunctions[value]"]').val().length - 1), 1) === "|"){
								$('input[name="inputFunctions[value]"]').val($('input[name="inputFunctions[value]"]').val() + str);
							} else {
								$('input[name="inputFunctions[value]"]').val($('input[name="inputFunctions[value]"]').val() + "|" + str);
							}
							
							// Regenerate the display 
							generateFunctionsDisplay($('input[name="inputFunctions[value]"]').val());
							
							// Reset the fields for the next one 
							$('input[name="inputFunctions[name]"]').val("");
							$('input[name="inputFunctions[call]"]').val("");
						}
					});
				});
				
				function checkForm(){
					if($("#inputDisplayName").val() == ""){
						$("#formMessages").html("You need to enter a name").removeClass("hidden");
						return false;
					}
					
					return true;
				}
				
				function cancel(){
					window.location.replace("<?php echo Url::getAdminHttpBase(); ?>/index.php?option=component&act=manage");
				}
				</script>
				<div id="formMessages" class="alert alert-error hidden"></div>
				<form name="adminForm" id="adminForm" method="post" action="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=component&act=<?php echo (Url::getParts('act') === "edit" ? "edit" . (strlen($id) > 0 ? "&id=" . $id : "") : "add"); ?>&result=1">
					<ul id="componentTab" class="nav nav-tabs">
						<li class="active"><a href="#general" data-toggle="tab">General</a></li>
						<li><a href="#functions" data-toggle="tab">Functions</a></li>
					</ul>
					<div id="componentTabContent" class="tab-content">
						<div class="tab-pane fade in active" id="general">
							<div class="control-group">
								<label class="control-label" for="inputDisplayName">Name</label>
								<div class="controls">
									<input type="text" name="inputDisplayName" id="inputDisplayName" value="<?php echo (strlen($id) > 0 ? $data->getDisplayName(1) : ""); ?>" />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="inputDefaultUrl">Default URL</label>
								<div class="controls">
									<input type="text" name="inputDefaultUrl" id="inputDefault" value="<?php echo (strlen($id) > 0 ? $data->getDefaultUrl() : ""); ?>" />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="inputActive">Active</label>
								<div class="controls">
									<select name="inputActive" id="inputActive">
										<option value="0"<?php echo (strlen($id) > 0 ? ($data->getActive() == 0 ? "selected=\"selected\"" : "") : "selected=\"selected\""); ?>>No</option>
										<option value="1"<?php echo (strlen($id) > 0 && $data->getActive() == 1 ? "selected=\"selected\"" : ""); ?>>Yes</option>
									</select>
								</div>
							</div>
						</div>
						<div class="tab-pane fade in" id="functions">
							<div class="control-group">
								<label class="control-label" for="inputFunctions">Functions</label>
								<div id="functionsDisplay">
									
								</div>
								<div class="controls">
									<input type="text" name="inputFunctions[name]" id="inputFunctions[name]" value="" placeholder="Name" />
									<input type="text" name="inputFunctions[call]" id="inputFunctions[call]" value="" placeholder="Caller" />
									<a href="#" id="addOptionItem"><i class="icon-plus"></i> Add</a>
								</div>
							</div>
							<input type="hidden" name="inputFunctions[value]" id="inputFunctions[value]" value="<?php echo (strlen($id) > 0 ? $data->getFunctions() : ""); ?>" />
						</div>
					</div>
					<div class="controls">
						<div class="form-actions">
							<button type="submit" class="btn btn-primary" onClick="return checkForm();">Save changes</button>
							<button type="button" class="btn" onClick="return cancel();">Cancel</button>
						</div>
					</div>
					<script type="text/javascript">
					generateFunctionsDisplay($('input[name="inputFunctions[value]"]').val());
					</script>
				</form>
				<?php
			}
			
			if($result == 1){
				$info = Form::getParts();
				$error = false;
				$errorMessage = "";
				
				if(!isset($info['inputDisplayName']) || $info['inputDisplayName'] === ""){
					$error = true;
					$errorMessage = "Please enter a name";
				}
				
				if($error == false){
					// Save it
					$data->setDisplayName($info['inputDisplayName']);
					$data->setFunctions($info['inputFunctions']['value']);
					$data->setDefaultUrl($info['inputDefaultUrl']);
					$data->setActive($info['inputActive']);
					
					if($data->save() == true){
						if(strlen($id) > 0){
							Messages::setMessage("Component Saved", Define::get("MessageLevelSuccess"));
						} else {
							Messages::setMessage("Component Added", Define::get("MessageLevelSuccess"));
						}
						
						Url::redirect(Url::getAdminHttpBase() . "/index.php?option=component&act=manage", 0, false);
					}
				} else {
					Messages::setMessage($errorMessage, Define::get("MessageLevelError"));
					Url::redirect(Url::getAdminHttpBase() . "/index.php?option=component&act=" . (Url::getParts('act') === "edit" ? "edit" . ($id > 0 ? "&id=" . $id : "") : "add"), 0, false);
				}
			}
		} else {
			Messages::setMessage("You cannot edit a system function", Define::get("MessageLevelError"));
			Url::redirect(Url::getAdminHttpBase() . "/index.php?option=component&act=manage", 0, false);
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(Url::home(), 3, false);
	}
}

?>
