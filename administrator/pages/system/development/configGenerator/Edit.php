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
		ImportClass("Config.ConfigOption");
		
		$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
		$id = (Url::getParts('id') !== "" ? Url::getParts('id') : NULL);
		
		// Create the class
		$data = new ConfigOption($id);
		
		if($result == 0){
			// Display the page text
			echo Text::pageTitle((Url::getParts('task') === "edit" ? "Edit" : "Add") . " Configuration");
			?>
			<script type="text/javascript">
			function is_int(intValue){
				var intTest = /^[0-9]+$/;

				if(intTest.test(intValue)){
					return true;
				} else {
					return false;
				}
			}
			</script>
			
			<script type="text/javascript">
			function generateArrayTypeDisplay(inputString){
				// Break into individual parts 
				var parts = inputString.match(/(\w+\(\w+\=\<\w+\>\))|(\w+\(\w+\=\<\w+\([\w\|\(\)]+\)\>\))/g);
				
				var outputStr = '' + 
				'<table class=\"table table-bordered table-striped table-hover">' + 
					'<thead>' + 
						'<tr>' + 
							'<th>' + 
								'Name' + 
							'</th>' + 
							'<th>' + 
								'Index' + 
							'</th>' + 
							'<th>' + 
								'Type' + 
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
						var type = "";
						var match = row.match(/(\w+)\((\w+)\=\<(\d+)\>\)/);

						if(match == null){
							match = row.match(/(\w+)\((\w+)\=\<(\d+)(\([\w\|\(\)]+\))\>\)/);
						}
						
						if(match != null){
							switch(match[3]){
								case "1":
									type = "Integer";
									break;
								case "2":
									type = "Double";
									break;
								case "3":
									type = "Array";
									break;
								case "4":
									type = "TextBox";
									break;
								case "5":
									type = "TextArea";
									break;
								case "6":
									type = "Date";
									break;
								case "7":
									// Match 4 should be the options 
									if(match[4] != null){
										// Figure out the options 
										var options = match[4].match(/(\w+\(\w+\))/g);
										type = "Option";
										
										if(options != null){
											var optionStr = "";
											for(var o = 0; o < options.length; o++){
												var option = options[o].match(/(\w+)\((\w+)\)/);
												
												if(option[1] != null){
													optionStr += option[1] + ", ";
												}
											}

											if(optionStr.substr((optionStr.length - 2), 2) == ", "){
												optionStr = optionStr.substr(0, (optionStr.length - 2));
											}

											if(optionStr.length > 0){
												type += " (" + optionStr + ")";
											}
										}
									}
									
									break;
								case "8":
									type = "True/False";
									break;
								default:
									type = "Unknown";
									break;
							}
							
							outputStr += '' + 
							'<tr>' + 
								'<td>' + 
									match[1] + 
								'</td>' + 
								'<td>' + 
									match[2] + 
								'</td>' + 
								'<td>' + 
									type + 
								'</td>' + 
								'<td>' + 
									'<div class="pull-left"><i id="optionsArray_' + i + '" class="icon-pencil"></i></div>' + 
									'<div class="pull-right"><i id="optionsArray_' + i + '" class="icon-remove"></i></div>' + 
								'</td>' + 
							'</tr>';
						}
					}
				}
				
				outputStr += '' + 
					'</tbody>' + 
				'</table>';

				$("#optionsArrayDisplay").html(outputStr);
			}
			</script>
			
			<script type="text/javascript">
			function generateOptionTypeDisplay(inputString){
				// Break apart on the | 
				var parts = inputString.split("|");
				var outputStr = '' + 
				'<table class=\"table table-bordered table-striped table-hover">' + 
					'<thead>' + 
						'<tr>' + 
							'<th>' + 
								'Name' + 
							'</th>' + 
							'<th>' + 
								'Value' + 
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
						var match = row.match(/(\w+)\((\w+)\)/);
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
							'<div class="pull-left"><i id="optionsArray_' + i + '" class="icon-pencil"></i></div>' + 
							'<div class="pull-right"><i id="optionsArray_' + i + '" class="icon-remove"></i></div>' + 
						'</td>' + 
						'</tr>';
					}
				}
				
				outputStr += '' + 
					'</tbody>' + 
				'</table>';

				$("#optionsOptionDisplay").html(outputStr);
			}
			</script>
			
			<script type="text/javascript">
			$(document).ready(function(){ 
				$("#inputType").change(function(e){
					var selectedValue = $(this).find("option:selected").val();
					var selectVal = "options_" + selectedValue;
					
					if(selectedValue > 0){
						// Set all of the options to hidden if not selected value 
						$('div[id^="options_"]').each(function(index){
							if(selectVal == this.id){
								if($("#" + this.id).hasClass("hidden")){
									$("#" + this.id).removeClass("hidden");
								}
							} else {
								if(!$("#" + this.id).hasClass("hidden")){
									$("#" + this.id).addClass("hidden");
								}
							}
						});
					} else {
						// Turn everything off 
						$('div[id^="options_"]').each(function(index){
							var currentId = this.id;
							
							if(!$("#" + currentId).hasClass("hidden")){
								$("#" + currentId).addClass("hidden");
							}
						});
					}
				});

				$("#addArrayItem").click(function(e){
					// Format: DisplayName(indexName=<type(options)>) 
					var displayName = $('input[name="inputOptions[3][name]"]').val();
					var indexName = $('input[name="inputOptions[3][index]"]').val();
					var type = $('select[name="inputOptions[3][type]"]').val();

					if(displayName.length == 0){

					} else

					if(indexName.length == 0){

					} else 

					if(type.length == 0){

					} else {
						// All good so add to the field 
						var str = "";
						var failure = false; 
						var failureStr = "";
						
						if(type == 7){
							var options = $('input[name="inputOptions[3][options][option]"]').val();

							if(options.length > 0){
								str = displayName + "(" + indexName + "=<" + type + "(" + options + ")>)";
							} else {
								failure = true;
								failureStr = "";
							}
						} else {
							str = displayName + "(" + indexName + "=<" + type + ">)";
						}
						
						if(failure == false){
							if($('input[name="inputOptions[3][value]"]').val().length == 0 || $('input[name="inputOptions[3][value]"]').val().substr(($('input[name="inputOptions[3][value]"]').val().length - 1), 1) === "|"){
								$('input[name="inputOptions[3][value]"]').val($('input[name="inputOptions[3][value]"]').val() + str);
							} else {
								$('input[name="inputOptions[3][value]"]').val($('input[name="inputOptions[3][value]"]').val() + "|" + str);
							}
							
							// Regenerate the display 
							generateArrayTypeDisplay($('input[name="inputOptions[3][value]"]').val());
							
							// Reset the fields for the next one 
							$('input[name="inputOptions[3][name]"]').val("");
							$('input[name="inputOptions[3][index]"]').val("");
							$('select[name="inputOptions[3][type]"]').val(0);
						}
					}
				});
				
				$("#addOptionItem").click(function(e){
					// Format: DisplayName(indexName=<type(options)>) 
					var displayName = $('input[name="inputOptions[7][name]"]').val();
					var indexName = $('input[name="inputOptions[7][value]"]').val();

					if(displayName.length == 0){

					} else

					if(indexName.length == 0){

					} else {
						// All good so add to the field 
						var str = displayName + "(" + indexName + ")";
						if($('input[name="inputOptions[7]"]').val().length == 0 || $('input[name="inputOptions[7]"]').val().substr(($('input[name="inputOptions[7]"]').val().length - 1), 1) === "|"){
							$('input[name="inputOptions[7]"]').val($('input[name="inputOptions[7]"]').val() + str);
						} else {
							$('input[name="inputOptions[7]"]').val($('input[name="inputOptions[7]"]').val() + "|" + str);
						}
						
						
						// Regenerate the display 
						generateOptionTypeDisplay($('input[name="inputOptions[7]"]').val());
						
						// Reset the fields for the next one 
						$('input[name="inputOptions[7][name]"]').val("");
						$('input[name="inputOptions[7][value]"]').val("");
					}
				});
				
				$(".icon-remove").click(function(e){
					var id = $(this).attr('id');
					
					if(id !== ""){
						if(!$(this).hasClass("icon-white")){
							bootbox.confirm("Are you sure you want to delete this?", function(result){
								if(result == true){
									// Split apart the id 
									var idParts = id.split("_");
									
									if(idParts[0] === "optionsArray"){
										var parts = $('input[name="inputOptions[3]"]').val().split("|");
										var outputStr = "";
										
										for(var i = 0; i < parts.length; i++){
											if(i != idParts[1]){
												if(outputStr.length == 0 || outputStr.substr((outputStr.length - 1), 1) === "|"){
													outputStr += parts[i];
												} else {
													outputStr += "|" + parts[i];
												}
											}
										}
										
										$('input[name="inputOptions[3]"]').val(outputStr);
										generateOptionsArrayDisplay($('input[name="inputOptions[3]"]').val());
									}
								}
							}); 
						}
					}
				});
				
				$('select[name="inputOptions[3][type]"]').change(function(){
					var value = $(this).find("option:selected").val();
					
					//inputOptions[3][options] 
					if(value == 1){
						// Integer 
						// Max length 
						$("#inputOptions_3_option").html("<input type=\"text\" name=\"inputOptions[3][options][maxLength]\" id=\"inputOptions[3][options][maxlength]\" value=\"\" placeholder=\"Max Length\" />");
					} else if(value == 2){
						// Double 
						// Max length and decimal length 
						$("#inputOptions_3_option").html("<input type=\"text\" name=\"inputOptions[3][options][maxLength]\" id=\"inputOptions[3][options][maxlength]\" value=\"\" placeholder=\"Max Length\" /><input type=\"text\" name=\"inputOptions[3][options][decimalLength]\" id=\"inputOptions[3][options][decimalLength]\" value=\"\" placeholder=\"Decimal Length\" />");
					} else if(value == 3){
						// Array - Not supported 
					} else if(value == 4){
						// TextBox 
						// Max length 
						$("#inputOptions_3_option").html("<input type=\"text\" name=\"inputOptions[3][options][maxLength]\" id=\"inputOptions[3][options][maxlength]\" value=\"\" placeholder=\"Max Length\" />");
					} else if(value == 5){
						// TextArea 
						// WYSIWYG 
						$("#inputOptions_3_option").html("<select name=\"inputOptions[3][options][wysiwyg]\" id=\"inputOptions[3][options][wysiwyg]\">" + 
							"<option value=\"\"> - Use WYSIWYG - </option>" + 
							"<option value=\"1\">Yes</option>" + 
							"<option value=\"0\">No</option>" + 
						"</select>");
					} else if(value == 6){
						// Date 
						// Format 
						$("#inputOptions_3_option").html("<select name=\"inputOptions[3][options][format]\" id=\"inputOptions[3][options][format]\">" + 
							"<option value=\"\">- Select Format-</option>" + 
							"<option value=\"MMDDYYYY\">MMDDYYYY</option>" + 
							"<option value=\"MM/DD/YYYY\">MM/DD/YYYY</option>" + 
							"<option value=\"MM-DD-YYYY\">MM-DD-YYYY</option>" + 
							"<option value=\"YYYYMMDD\">YYYYMMDD</option>" + 
						"</select>");
					} else if(value == 7){
						// Options 
						// DisplayName(Value) 
						$("#inputOptions_3_option").html("<a href=\"#optionModal\" role=\"button\" class=\"btn\" data-toggle=\"modal\">Add Option</a><input type=\"hidden\" name=\"inputOptions[3][options][option]\" id=\"inputOptions[3][options][option]\" />");
					} else if(value == 8){
						// True/False 
						// Format 
						$("#inputOptions_3_option").html("<select name=\"inputOptions[3][options][format]\" id=\"inputOptions[3][options][format]\">" + 
							"<option value=\"\">- Select Format -</option>" + 
							"<option value=\"Y/N\">Yes/No</option>" + 
							"<option value=\"O/O\">On/Off</option>" + 
							"<option value=\"T/F\">True/False</option>" + 
						"</select>");
					} else {
						$("#inputOptions_3_option").html("");
					}
				});
				
				$('#optionModal .save-button').click('hidden', function(e){
					// Value Format: DisplayName(value)|DisplayName(value) 
					var failure = false; 
					var failureStr = "";
					var outputStr = "";
					var arrayId = 0;
					
					// Get all of the fields 
					// Assume the first one is the name and the second is the value 
					$('input[id^="optionModal_"]').each(function(){
						var id = this.id;

						if($("#" + id).val().length > 0){
							if(outputStr.length == 0){
								outputStr += $("#" + id).val();
							} else {
								outputStr += "(" + $("#" + id).val() + ")";
							}
							$("#" + id).val("");
						} else {
							failureStr = failureStr + "You need to fill in the field " + $("#" + id).attr('placeholder') + "<br />";
							failure = true;
						}
					});
					
					// All good... close the popup 
					if(failure == false){
						if($('input[name="inputOptions[3][options][option]"]').val().length == 0 || $('input[name="inputOptions[3][options][option]"]').val().substr(($('input[name="inputOptions[3][options][option]"]').val().length - 1), 1) === "|"){
							$('input[name="inputOptions[3][options][option]"]').val($('input[name="inputOptions[3][options][option]"]').val() + outputStr);
						} else {
							$('input[name="inputOptions[3][options][option]"]').val($('input[name="inputOptions[3][options][option]"]').val() + "|" + outputStr);
						}

						$('#optionModal').modal('hide');
					} else {
						// Display the error message 
						$("#optionModalMessages").html(failureStr).removeClass("hidden");
						alert("error");
					}
				});
			});
			</script>
			
			<script type="text/javascript">
			function checkForm(){
				if($("#inputComponent").val() == ""){
					$("#formMessages").html("You need to select a component").removeClass("hidden");
					return false;
				}

				if($("#inputType").val() == ""){
					$("#formMessages").html("You need to select a type").removeClass("hidden");
					return false;
				}

				// Check options based on type 
				if($("#inputType").val() > 0){
					if($("#inputType").val() == 1){
						// Check if there is a max length defined (optional) 
						if($('input[name="inputOptions[1]"]').val().length > 0 && is_int($('input[name="inputOptions[1]"]').val()) == false){
							$("#formMessages").html("You need to enter the max length as a number").removeClass("hidden");
							return false;
						}
					} else if($("#inputType").val() == 2){
						// Check if a max length defined (optional) 
						if($('input[name="inputOptions[2][length]"]').val().length > 0 && is_int($('input[name="inputOptions[2][length]"]').val()) == false){
							$("#formMessages").html("You need to enter the max length as a number").removeClass("hidden");
							return false;
						}
						
						// Check if a decimal length defined (optional) 
						if($('input[name="inputOptions[2][decimal]"]').val().length > 0 && is_int($('input[name="inputOptions[2][decimal]"]').val()) == false){
							$("#formMessages").html("You need to enter the decimal length as a number").removeClass("hidden");
							return false;
						}
					} else if($("#inputType").val() == 3){
						// Format: DisplayName(indexName=<type(options)>) 
						var parts = $('input[name="inputOptions[3]"]').val().split("|");
						
						for(var i = 0; i < parts.length; i++){
							if(parts[i].length > 0){
								var match = parts[i].match(/(\w+)\((\w+)\=(\d+)\)/);
								
								// Check if there is a display name (required) 
								if(match[1].length == 0){
									$("#formMessages").html("(" + i + ") You need to enter a display name").removeClass("hidden");
									return false;
								}
								
								// Check if there is an indexName (required) 
								if(match[2].length == 0){
									$("#formMessages").html("(" + i + ") You need to enter an index name").removeClass("hidden");
									return false;
								}
								
								// Check if there is a type (required) 
								if(match[3].length == 0){
									$("#formMessages").html("(" + i + ") You need to select a type").removeClass("hidden");
									return false;
								}
								
								// Check if there is a type option (optional) 
								// TODO: NOT SUPPORTED 
							}
						}
					} else if($("#inputType").val() == 4){
						// Check if a max length defined (optional) 
						if($('input[name="inputOptions[4]"]').val().length > 0 && is_int($('input[name="inputOptions[4]"]').val()) == false){
							$("#formMessages").html("You need to enter the max length as a number").removeClass("hidden");
							return false;
						}
					} else if($("#inputType").val() == 5){
						// Check if a format is defined (required) 
						if($('select[name="inputOptions[5]"]').find("option:selected").val().length == 0){
							$("#formMessages").html("You need to determine WYSIWYG").removeClass("hidden");
							return false;
						}
					} else if($("#inputType").val() == 6){
						// Check if a format is defined (required) 
						if($('select[name="inputOptions[6]"]').find("option:selected").val().length == 0){
							$("#formMessages").html("You need to select a date format").removeClass("hidden");
							return false;
						}
					} else if($("#inputType").val() == 7){
						// Format: DisplayName(value) 
						var parts = $('input[name="inputOptions[7]"]').val().split("|");
						
						for(var i = 0; i < parts.length; i++){
							if(parts[i].length > 0){
								var match = parts[i].match(/(\w+)\((\w+)/);
								
								// Check if there is a display name (required) 
								if(match[1].length == 0){
									$("#formMessages").html("(" + i + ") You need to enter a display name").removeClass("hidden");
									return false;
								}
								
								// Check if there is a value (required) 
								if(match[2].length == 0){
									$("#formMessages").html("(" + i + ") You need to enter a value").removeClass("hidden");
									return false;
								}
							}
						}
					} else if($("#inputType").val() == 8){
						// Check if a format is defined (required) 
						if($('select[name="inputOptions[8]"]').find("option:selected").val().length == 0){
							$("#formMessages").html("You need to select a format").removeClass("hidden");
							return false;
						}
					}
				}
				
				if($("#inputName").val() == ""){
					$("#formMessages").html("You need to enter a name").removeClass("hidden");
					return false;
				}
				
				if($("#inputIndex").val() == ""){
					$("#formMessages").html("You need to enter an index").removeClass("hidden");
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
				<div class="modal fade" id="optionModal">
					<div class="modal-header">
						<a class="close" data-dismiss="modal">&times;</a>
						<h3>Add Item</h3>
					</div>
					<div class="modal-body">
						<div id="optionModalMessages" class="alert alert-error hidden"></div>
						<input type="text" name="optionModal_name" id="optionModal_name" value="" placeholder="Name" />
						<input type="text" name="optionModal_value" id="optionModal_value" value="" placeholder="Value" />
					</div>
					<div class="modal-footer">
						<a href="#" class="btn" data-dismiss="modal">Close</a>
						<a href="#" class="btn save-button btn-primary">Save Changes</a>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputComponent">Component</label>
					<div class="controls">
						<select name="inputComponent" id="inputComponent">
							<option value=""<?php echo ($id > 0 ? ($data->getComponent() == 0 ? " selected=\"selected\"" : "") : " selected=\"selected\""); ?>>- Select -</option>
							<?php
							ImportClass("Component.Components");
							
							$components = new Components();
							if($components->rowsExist()){
								while($components->hasNext()){
									$row = $components->getNext();
							?>
							<option value="<?php echo $row->getId(); ?>"<?php echo ($id > 0 ? ($data->getComponent() == $row->getId() ? " selected=\"selected\"" : "") : ""); ?>><?php echo $row->getName(); ?></option>
							<?php 
								}
							}
							?>
						</select>
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
							<option value="<?php echo $row['id']; ?>"<?php echo ($id > 0 ? ($data->getType() == $row['id'] ? " selected=\"selected\"" : "") : ""); ?>><?php echo $row['name']; ?></option>
							<?php 
								}
							}
							?>
						</select>
					</div>
				</div>
				<div id="options_1"<?php echo ($id > 0 ? ($data->getType() == 1 ? "" : " class=\"hidden\"") : " class=\"hidden\""); ?>>
					<div class="control-group">
						<label class="control-label" for="inputOptions[1]">Max Length</label>
						<div class="controls">
							<input type="text" name="inputOptions[1]" id="inputOptions_1" value="<?php echo ($id > 0 ? $data->getOptions(1) : ""); ?>" />
						</div>
					</div>
				</div>
				<div id="options_2"<?php echo ($id > 0 ? ($data->getType() == 2 ? "" : " class=\"hidden\"") : " class=\"hidden\""); ?>>
					<?php 
					if($id > 0 && $data->getType() == 2){
						list($length, $decimal) = $data->getOptions(1);
					}
					?>
					<div class="control-group">
						<label class="control-label" for="inputOptions[2][length]">Max Length</label>
						<div class="controls">
							<input type="text" name="inputOptions[2][length]" id="inputOptions[2][length]" value="<?php echo (isset($length) ? $length : ""); ?>" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputOptions[2][decimal]">Max Decimal Length</label>
						<div class="controls">
							<input type="text" name="inputOptions[2][decimal]" id="inputOptions[2][decimal]" value="<?php echo (isset($decimal) ? $decimal : ""); ?>" />
						</div>
					</div>
				</div>
				<div id="options_3"<?php echo ($id > 0 ? ($data->getType() == 3 ? "" : " class=\"hidden\"") : " class=\"hidden\""); ?>>
					<div class="control-group">
						<label class="control-label" for="inputOptions[3]">Fields</label>
						<div id="optionsArrayDisplay">
							
						</div>
						<div class="controls">
							<input type="text" name="inputOptions[3][name]" id="inputOptions[3][name]" value="" placeholder="Name" />
							<input type="text" name="inputOptions[3][index]" id="inputOptions[3][index]" value="" placeholder="Index" />
							<select name="inputOptions[3][type]" id="inputOptions[3][type]">
								<option value=""<?php echo ($id > 0 ? ($data->getType() == 0 ? " selected=\"selected\"" : "") : " selected=\"selected\""); ?>>- Type -</option>
								<?php
								ConfigTypes::reset();
								
								if(ConfigTypes::rowsExist()){
									while(ConfigTypes::hasNext()){
										$row = ConfigTypes::getNext();
										
										// Remove arrays -- not supported
										if($row['id'] != 3){
								?>
								<option value="<?php echo $row['id']; ?>"<?php echo ($id > 0 ? ($data->getType() == $row['id'] ? " selected=\"selected\"" : "") : ""); ?>><?php echo $row['name']; ?></option>
								<?php 
										}
									}
								}
								?>
							</select>
							<span id="inputOptions_3_option"></span>
							<a href="#" id="addArrayItem"><i class="icon-plus"></i> Add</a>
							<input type="hidden" name="inputOptions[3][options]" id="inputOptions[3][options]" value="<?php echo ($id > 0 ? $data->getOptions(1) : ""); ?>" />
						</div>
					</div>
					<input type="hidden" name="inputOptions[3][value]" id="inputOptions[3][value]" value="<?php echo ($id > 0 ? $data->getOptions(1) : ""); ?>" />
				</div>
				<div id="options_4"<?php echo ($id > 0 ? ($data->getType() == 4 ? "" : " class=\"hidden\"") : " class=\"hidden\""); ?>>
					<div class="control-group">
						<label class="control-label" for="inputOptions[4]">Max Length</label>
						<div class="controls">
							<input type="text" name="inputOptions[4]" id="inputOptions[4]" value="<?php echo ($id > 0 ? $data->getOptions(1) : ""); ?>" />
						</div>
					</div>
				</div>
				<div id="options_5"<?php echo ($id > 0 ? ($data->getType() == 5 ? "" : " class=\"hidden\"") : " class=\"hidden\""); ?>>
					<div class="control-group">
						<label class="control-label" for="inputOptions[5]">WYSIWYG</label>
						<div class="controls">
							<select name="inputOptions[5]" id="inputOptions[5]">
								<option value="1"<?php echo ($id > 0 ? ($data->getOptions(1) === "1" ? " selected=\"selected\"" : "") : " selected=\"selected\""); ?>>Yes</option>
								<option value="0"<?php echo ($id > 0 ? ($data->getOptions(1) === "0" ? " selected=\"selected\"" : "") : ""); ?>>No</option>
							</select>
						</div>
					</div>
				</div>
				<div id="options_6"<?php echo ($id > 0 ? ($data->getType() == 6 ? "" : " class=\"hidden\"") : " class=\"hidden\""); ?>>
					<div class="control-group">
						<label class="control-label" for="inputOptions[6]">Format</label>
						<div class="controls">
							<select name="inputOptions[6]" id="inputOptions[6]">
								<option value=""<?php echo ($id > 0 ? ($data->getOptions(1) === "" ? " selected=\"selected\"" : "") : " selected=\"selected\""); ?>>- Select -</option>
								<option value="MMDDYYYY"<?php echo ($id > 0 ? ($data->getOptions(1) === "MMDDYYYY" ? " selected=\"selected\"" : "") : ""); ?>>MMDDYYYY</option>
								<option value="MM/DD/YYYY"<?php echo ($id > 0 ? ($data->getOptions(1) === "MM/DD/YYYY" ? " selected=\"selected\"" : "") : ""); ?>>MM/DD/YYYY</option>
								<option value="MM-DD-YYYY"<?php echo ($id > 0 ? ($data->getOptions(1) === "MM-DD-YYYY" ? " selected=\"selected\"" : "") : ""); ?>>MM-DD-YYYY</option>
								<option value="YYYYMMDD"<?php echo ($id > 0 ? ($data->getOptions(1) === "YYYYMMDD" ? " selected=\"selected\"" : "") : ""); ?>>YYYYMMDD</option>
							</select>
						</div>
					</div>
				</div>
				<div id="options_7"<?php echo ($id > 0 ? ($data->getType() == 7 ? "" : " class=\"hidden\"") : " class=\"hidden\""); ?>>
					<div class="control-group">
						<label class="control-label" for="inputOptions[7]">Fields</label>
						<div id="optionsOptionDisplay">
							
						</div>
						<div class="controls">
							<input type="text" name="inputOptions[7][name]" id="inputOptions[7][name]" value="" placeholder="Name" />
							<input type="text" name="inputOptions[7][value]" id="inputOptions[7][value]" value="" placeholder="Value" />
							<a href="#" id="addOptionItem"><i class="icon-plus"></i> Add</a>
						</div>
					</div>
					<input type="hidden" name="inputOptions[7][value]" id="inputOptions[7][value]" value="<?php echo ($id > 0 ? $data->getOptions(1) : ""); ?>" />
				</div>
				<div id="options_8"<?php echo ($id > 0 ? ($data->getType() == 8 ? "" : " class=\"hidden\"") : " class=\"hidden\""); ?>>
					<div class="control-group">
						<label class="control-label" for="inputOptions[8]">Format</label>
						<div class="controls">
							<select name="inputOptions[8]" id="inputOptions[8]">
								<option value=""<?php echo ($id > 0 ? ($data->getOptions(1) === "" ? " selected=\"selected\"" : "") : " selected=\"selected\""); ?>>- Select -</option>
								<option value="Y/N"<?php echo ($id > 0 ? ($data->getOptions(1) === "Y/N" ? " selected=\"selected\"" : "") : ""); ?>>Yes/No</option>
								<option value="O/O"<?php echo ($id > 0 ? ($data->getOptions(1) === "O/O" ? " selected=\"selected\"" : "") : ""); ?>>On/Off</option>
								<option value="T/F"<?php echo ($id > 0 ? ($data->getOptions(1) === "T/F" ? " selected=\"selected\"" : "") : ""); ?>>True/False</option>
							</select>
						</div>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputName">Name</label>
					<div class="controls">
						<input type="text" name="inputName" id="inputName" value="<?php echo ($id > 0 ? $data->getName(1) : ""); ?>" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputIndex">Index</label>
					<div class="controls">
						<input type="text" name="inputIndex" id="inputIndex" value="<?php echo ($id > 0 ? $data->getIndex(1) : ""); ?>" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputDeleteCheck">Delete Check</label>
					<div class="controls">
						<input type="text" name="inputDeleteCheck" id="inputDeleteCheck" value="<?php echo ($id > 0 ? $data->getDeleteCheck(1) : ""); ?>" />
					</div>
				</div>
				<div class="controls">
					<div class="form-actions">
						<button type="submit" class="btn btn-primary" onClick="return checkForm();">Save changes</button>
						<button type="button" class="btn" onClick="return cancel();">Cancel</button>
					</div>
				</div>
				<script type="text/javascript">
				<?php
				if($id > 0 && $data->getType() == 3){
					?>
				generateArrayTypeDisplay($('input[name="inputOptions[3][value]"]').val());
					<?php
				}
				
				if($id > 0 && $data->getType() == 7){
					?>
				generateOptionTypeDisplay($('input[name="inputOptions[7][value]"]').val());
					<?php
				}
				?>
				</script>
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
			
			// Check the options base on the type
			if($error == false){
				switch($info['inputType']){
					case 1:
						// Check if there is a max length defined (optional)
						if(strlen($info['inputOptions'][1]) > 0 && is_int((int)$info['inputOptions'][1]) == false){
							$error = true;
							$errorMessage = "You need to enter the max length as a number";
						}
						break;	
					case 2:
						// Check if a max length defined (optional)
						if(strlen($info['inputOptions'][2]['length']) > 0 && is_int((int)$info['inputOptions'][2]['length']) == false){ // TODO: Fix the typecast
							$error = true;
							$errorMessage = "You need to enter the max length as a number";
						}
							
						// Check if a decimal length defined (optional)
						if(strlen($info['inputOptions'][2]['decimal']) > 0 && is_int((int)$info['inputOptions'][2]['decimal']) == false){ // TODO: Fix the typecast
							$error = true;
							$errorMessage = "You need to enter the decimal length as a number";
						}
						break;	
					case 3:
						// Format: DisplayName(indexName=<type(options)>)
						preg_match_all('/(\w+\(\w+\=\<\w+\>\))|(\w+\(\w+\=\<\w+\([\w\|\(\)]+\)\>\))/', $info['inputOptions'][3]['value'], $parts);
						$parts = $parts[0];
						
						for($i = 0; $i < count($parts); $i++){
							if(strlen($parts[$i]) > 0){
								preg_match('/(\w+)\((\w+)\=\<(\d+)\>\)/', $parts[$i], $matches);
								
								if(count($matches) == 0){
									preg_match('/(\w+)\((\w+)\=\<(\d+)(\([\w\|\(\)]+\))\>\)/', $parts[$i], $matches);
								}
								
								// Check if there is a display name (required)
								if(strlen($matches[1]) == 0){
									$error = true;
									$errorMessage = "(" . $i . ") You need to enter a display name";
								}
								
								// Check if there is an indexName (required)
								if(strlen($matches[2]) == 0){
									$error = true;
									$errorMessage = "(" . $i . ") You need to enter an index name";
								}
								
								// Check if there is a type (required)
								if(strlen($matches[3]) == 0){
									$error = true;
									$errorMessage = "(" . $i . ") You need to select a type";
								}
								
								// Check if there is a type option (optional)
								// TODO: NOT SUPPORTED
							}
						}
						break;	
					case 4:
						// Check if a max length defined (optional)
						if(strlen($info['inputOptions'][4]) > 0 && is_int((int)$info['inputOptions'][4]) == false){ // TODO: Fix the typecast
							$error = true;
							$errorMessage = "You need to enter the max length as a number";
						}
						break;	
					case 5:
						// Check if WYSIWYG is needed (required)
						if(strlen($info['inputOptions'][5]) == 0){
							$error = true;
							$errorMessage = "You need to determine WYSIWYG";
						}
						break;	
					case 6:
						// Check if a format is defined (required)
						if(strlen($info['inputOptions'][6]) == 0){
							$error = true;
							$errorMessage = "You need to select a date format";
						}
						break;	
					case 7:
						// Format: DisplayName(value)
						$parts = explode("|", $info['inputOptions'][7]);
							
						for($i = 0; $i < count($parts); $i++){
							if(strlen($parts[$i]) > 0){
								preg_match('/(\w+)\((\w+)\)/', $parts[$i], $matches);
								
								// Check if there is a display name (required)
								if(strlen($matches[1]) == 0){
									$error = true;
									$errorMessage = "(" . $i . ") You need to enter a display name";
								}
									
								// Check if there is a value (required)
								if(strlen($matches[2]) == 0){
									$error = true;
									$errorMessage = "(" . $i . ") You need to enter a value";
								}
							}
						}
						break;	
					case 8:
						// Check if a format defined (required)
						if(strlen($info['inputOptions'][8]) == 0){
							$error = true;
							$errorMessage = "You need to select a option format";
						}
						break;	
					default:
						// TODO: Handle unknown type
						break;	
				}
			}
			
			if(!isset($info['inputName']) || $info['inputName'] === ""){
				$error = true;
				$errorMessage = "Please enter a name";
			}
			
			if(!isset($info['inputIndex']) || $info['inputIndex'] === ""){
				$error = true;
				$errorMessage = "Please enter an index";
			}
			
			if($error == false){
				// Save it
				$data->setComponent($info['inputComponent']);
				$data->setType($info['inputType']);
				
				switch($info['inputType']){
					case 1: // Integer
						$data->setOptions(array($info['inputOptions'][1]));
						break;
					case 2: // Double
						$data->setOptions(array($info['inputOptions'][2]['length'], $info['inputOptions'][2]['decimal']));
						break;
					case 3: // Array
						$data->setOptions(array($info['inputOptions'][3]['value']));
						break;
					case 4: // TextBox
						$data->setOptions(array($info['inputOptions'][4]));
						break;
					case 5: // TextArea
						$data->setOptions(array($info['inputOptions'][5]));
						break;
					case 6: // Date
						$data->setOptions(array($info['inputOptions'][6]));
						break;
					case 7: // Option
						$data->setOptions(array($info['inputOptions'][7]));
						break;
					case 8: // True/False
						$data->setOptions(array($info['inputOptions'][8]));
						break;
					default:
						// TODO: Handle unknown type
						break;
				}
				
				$data->setName($info['inputName']);
				$data->setIndex($info['inputIndex']);
				$data->setDeleteCheck($info['inputDeleteCheck']);
				
				if($data->save() > 0){
					if($id > 0){
						Messages::setMessage("Config Saved", Define::get("MessageLevelSuccess"));
					} else {
						Messages::setMessage("Config Added", Define::get("MessageLevelSuccess"));
					}
				} else {
					Messages::setMessage("Config not saved", Define::get("MessageLevelError"));
				}
				
				Url::redirect(Url::getAdminHttpBase() . "/index.php?option=development&act=configGenerator&task=manage", 0, false);
			} else {
				Messages::setMessage($errorMessage, Define::get("MessageLevelError"));
				Url::redirect(Url::getAdminHttpBase() . "/index.php?option=development&act=configGenerator&task=" . (Url::getParts('task') === "edit" ? "edit" . ($id > 0 ? "&id=" . $id : "") : "add"), 0, false);
			}
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(Url::home(), 3, false);
	}
}

?>
