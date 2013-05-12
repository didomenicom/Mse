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
		$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
		$com = (Url::getParts('com') !== "" ? Url::getParts('com') : NULL);
		
		if($com != NULL){
			ImportClass("Component.Component");
			ImportClass("Config.ConfigOptions");
			
			// Create the component class
			$component = new Component($com);
			
			// Get all of the options
			$filter['component'] = $component->getId();
			$configItems = new ConfigOptions($filter);
			
			if($result == 0){
				// Display the page text
				echo Text::pageTitle($component->getName(1) . " Configuration");
				?>
				<script type="text/javascript">
				$(document).ready(function(){ 
					$(".inputInteger").change(function(e){
						var intTest = /^[0-9]+$/;

						if(intTest.test($(this).val())){
							if($(this).parentsUntil("control-group").hasClass("error")){
								$(this).parentsUntil("control-group").removeClass("error");
							}
						} else {
							$(this).parentsUntil("control-group").addClass("error");
						}
					});
					
					$(".inputDouble").change(function(e){
						var doubleTest = /^(([0-9]+\.[0-9]+)|([0-9]+))$/;

						if(doubleTest.test($(this).val())){
							if($(this).parentsUntil("control-group").hasClass("error")){
								$(this).parentsUntil("control-group").removeClass("error");
							}
						} else {
							$(this).parentsUntil("control-group").addClass("error");
						}
					});
					
					$(".inputDate").change(function(e){
						var dateTest = /^((0?[1-9]|1[012])[- \/.](0?[1-9]|[12][0-9]|3[01])[- \/.](19|20)?[0-9]{2})*$/; // TODO: Handle no character between numbers 

						if(dateTest.test($(this).val())){
							if($(this).parentsUntil("control-group").hasClass("error")){
								$(this).parentsUntil("control-group").removeClass("error");
							}
						} else {
							$(this).parentsUntil("control-group").addClass("error");
						}
					});
					
					$(".icon-remove").click(function(e){
						var id = $(this).attr('id');

						if(id !== ""){
							if(!$(this).hasClass("icon-white")){
								bootbox.confirm("Are you sure you want to delete this?", function(result){
									if(result == true){
										// TODO: Complete 
									}
								}); 
							}
						}
					});
				});

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
					window.location.replace("<?php echo Url::getAdminHttpBase(); ?>/index.php?option=config&act=manage"); // TODO: 
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
						<label class="control-label" for="inputConfigOption[<?php echo $row->getId(); ?>]"><?php echo $row->getName(); ?></label>
						<div class="controls">
							<?php 
							if($row->getType() == 1){ // Integer
							?>
							<input type="text" class="inputInteger" name="inputConfigOption[<?php echo $row->getId(); ?>]" id="inputConfigOption[<?php echo $row->getId(); ?>]" value="<?php echo $row->getValue(); ?>" />
							<?php 
							}
							
							if($row->getType() == 2){ // Double
								// TODO: Add sig figs option 
							?>
							<input type="text" class="inputDouble" name="inputConfigOption[<?php echo $row->getId(); ?>]" id="inputConfigOption[<?php echo $row->getId(); ?>]" value="<?php echo $row->getValue(); ?>" />
							<?php 
							}
							
							if($row->getType() == 3){ // Array
								// Break the options array apart ('|' is the deliminator)
								$optionsArray = explode("|", $row->getOptions());
								$options = array();
								
								// Break apart the name and display name (Format: DisplayName(name))
								for($i = 0; $i < count($optionsArray); $i++){
									// Extract the name/display name
									preg_match_all("([0-9a-zA-Z]+)", $optionsArray[$i], $nameParts);
									
									if(count($nameParts[0]) > 0){
										list($options[$i]['displayName'], $options[$i]['name']) = $nameParts[0];
									}
								}
								
								// Break the values array apart ('|' is deliminator)
								$parts = explode("|", $row->getValue());
								
							?>
							<div class="input-large">
								<table class="table table-bordered table-striped table-hover">
									<thead>
										<tr>
											<?php 
											$columnCount = 1;
											
											foreach($options as $option){
											?>
											<th>
												<?php echo $option['displayName']; ?>
											</th>
											<?php 
												$columnCount++;
											}
											?>
											<th>
												
											</th>
										</tr>
									</thead>
									<tbody>
								<?php	
								for($i = 0; $i < count($parts); $i++){
									// Extract the name and type
									preg_match_all("([0-9a-zA-Z]+)", $parts[$i], $rowPartsArray);
									
									if(count($rowPartsArray) > 0){
										
										?>
										<tr>
											<?php 
											foreach($rowPartsArray[0] as $rowPart){
											?>
											<td>
												<?php echo $rowPart; ?>
											</td>
											<?php 
											}
											?>
											<td>
												<?php 
												$canDelete = true;
												
												if($row->getDeleteCheck() !== ""){
													// There is a function defined
													// Load the function 
													// Break apart the path
													$deleteCheckParts = explode("/", $row->getDeleteCheck());
														
													// Check if it is in valid format
													if(count($deleteCheckParts) > 0){
														$filename = array_pop($deleteCheckParts);
													
														// Check if the first part is "system"... system directory compared to the user directory
														$filePath = (strtolower(array_shift($deleteCheckParts)) === "system" ? "system".DS : "user".DS) . "configHandler".DS;
													
														foreach($deleteCheckParts as $deleteCheckPart){
															$filePath .= strtolower($deleteCheckPart).DS;
														}
													
														// Build path
														if(ImportFile(BASEPATH.DS.LIBRARY.DS . $filePath . $filename . ".php") == true){
															// Found and grabbed the file
															// Execute it
															$canDelete = DeleteCheck($row->getId() . "_" . $i);
														}
													}
												}
												
												if($canDelete){
												?>
												<i id="<?php echo $row->getId() . "_" . $i; ?>" class="icon-remove"></i>
												<?php 
												} else {
												?>
												<i id="<?php echo $row->getId() . "_" . $i; ?>" class="icon-white icon-remove"></i>
												<?php 
												}
												?>
											</td>
										</tr>
										<?php
									}
								}
								?>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="<?php echo $columnCount; ?>">
												<i class="icon-plus"></i> Add
											</td>
										</tr>
									</tfoot>
								</table>
							</div>
							
							<?php 
							}
							
							if($row->getType() == 4){ // Text Box
							?>
							<input type="text" name="inputConfigOption[<?php echo $row->getId(); ?>]" id="inputConfigOption[<?php echo $row->getId(); ?>]" value="<?php echo $row->getValue(); ?>" />
							<?php 
							}
							
							if($row->getType() == 5){ // Text Area
							?>
							<textarea name="inputConfigOption[<?php echo $row->getId(); ?>]" id="inputConfigOption[<?php echo $row->getId(); ?>]"><?php echo $row->getValue(); ?></textarea>
							<?php 
							}
							
							if($row->getType() == 6){ // Date
							?>
							<div class="input-append">
								<input type="text" class="inputDate" name="inputConfigOption[<?php echo $row->getId(); ?>]" id="inputConfigOption[<?php echo $row->getId(); ?>]" value="<?php echo $row->getValue(); ?>" />
								<span class="add-on"><i class="icon-calendar"></i></span>
							</div>
							<?php 
							}
													
							if($row->getType() == 7){ // Option
								// TODO: Implement
							?>
							Options not supported yet
							<?php 
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
					
					Url::redirect(Url::getAdminHttpBase() . "/index.php?option=development&act=configGenerator&task=manage", 0, false); // TODO: 
				} else {
					Messages::setMessage($errorMessage, Define::get("MessageLevelError"));
					Url::redirect(Url::getAdminHttpBase() . "/index.php?option=config&act=edit&com=" . $com, 0, false);
				}
			}
		} else {
			// No component specified
			// TODO: Error
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(UserFunctions::getLoginUrl(), 0, false);
	}
}

?>