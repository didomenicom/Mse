<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function EditArrayHeader(){
	// Notes about configs: 
	// 1. An index is required (this has been implemented in the jquery save)
	// 
	
	// TODO: Implement 'order' ordering
	// TODO: Require 'index' for any array 
	// TODO: Change new Array() to objects ('new Array()' should be '{}')
	// TODO: Remove \n from xml generation when this code is verified to work better
	// TODO: Fix xmlns and configrecord/configvalue adding in array save
	// TODO: Add support for icon-white
	// TODO: Add handler to clear fields on array add
	// TODO: Add option support for multiple options
	?>
	<script type="text/javascript">
	function generateArrayTypeDisplay(inputColumnStruct, inputRowStruct, outputDivStruct){
		// Break into individual parts 
		var columns = new Array();
		
		if(inputColumnStruct.val() != null){
			var columnXML = $.parseXML(inputColumnStruct.val());
			
			if(columnXML != null){
				var columnCount = 0;
				$(columnXML).find("config > configHeader").each(function(){
					columns[columnCount] = {};
					
					columns[columnCount]['name'] = $(this).children("displayText").text();
					columns[columnCount]['index'] = $(this).children("index").text();

					// The option tag is optional 
					// If there are no contents then don't include it in the array 
					if($(this).find("option").text() != ""){
						columns[columnCount]['options'] = new Array();
						
						var optionCount = 0;
						$(this).find("option > configHeader").each(function(){
							columns[columnCount]['options'][optionCount] = new Array();

							columns[columnCount]['options'][optionCount]['name'] = $(this).children("displayText").text();
							columns[columnCount]['options'][optionCount]['index'] = $(this).children("index").text();
							
							optionCount++;
						});
					}
					
					columnCount++;
				});
				
				var outputStr = '<table class=\"table table-bordered table-striped table-hover">' + 
					'<thead>' + 
						'<tr>';

						for(var i = 0; i < columns.length; i++){
							outputStr += 
							'<th>' + 
								columns[i]['name'] + 
							'</th>';
						}

						outputStr += 
						'<th width="5%">' + 
						'</th>';

						outputStr += '</tr>' + 
					'</thead>' + 
					'<tbody>';

				// Loop through the rows and create an array 
				var rowsXML = $.parseXML(inputRowStruct.val());
				
				if(rowsXML != null){
					var rowsArrayCount = 0;
					var rowsArray = new Array();
					
					// Process each record 
					$(rowsXML).find("config > configRecord").each(function(){
						rowsArray[rowsArrayCount] = new Array();
						
						// Process each value 
						$(this).children("configValue").each(function(){
							// Store the 'value' in an array indexed by 'index' 
							var index = $(this).children("index").text(); 
							rowsArray[rowsArrayCount][index] = new Array();
							rowsArray[rowsArrayCount][index]['value'] = $(this).children("value").text();
							
							// Check if this configValue has options and store them in the array indexed by 'options' 
							if($(this).find("options").text() != ""){
								rowsArray[rowsArrayCount][index]['options'] = new Array();
								
								var optionCount = 0;
								$(this).find("options > option").each(function(){
									// Add each option 
									rowsArray[rowsArrayCount][index]['options'][optionCount] = new Array();
									
									rowsArray[rowsArrayCount][index]['options'][optionCount]['name'] = $(this).children("displayName").text();
									rowsArray[rowsArrayCount][index]['options'][optionCount]['index'] = $(this).children("index").text();
									
									optionCount++;
								});
							}
						});
						
						rowsArrayCount++;
					});
					
					for(var i = 0; i < rowsArrayCount; i++){
						outputStr += '<tr>';
						
						for(var x = 0; x < columns.length; x++){
							outputStr += '<td>';
							
							if(rowsArray[i][columns[x]['index']] != null){
								// Check the column type 
								// If the options exist, then the value is one of the options 
								// Otherwise the value is considered a "string" 
								if(columns[x]['options'] != null){
									// This column has options 
									for(var o = 0; o < columns[x]['options'].length; o++){
										if(columns[x]['options'][o]['index'] == rowsArray[i][columns[x]['index']]['value']){
											outputStr += columns[x]['options'][o]['name'];
										}
									}
									
									// Check if the row has options 
									if(rowsArray[i][columns[x]['index']]['options'] != null && $.isArray(rowsArray[i][columns[x]['index']]['options'])){
										outputStr += " (";
										
										for(var o = 0; o < rowsArray[i][columns[x]['index']]['options'].length; o++){
											outputStr += rowsArray[i][columns[x]['index']]['options'][o]['name'];
											
											if(o != (rowsArray[i][columns[x]['index']]['options'].length - 1)){
												outputStr += ", ";
											}
										}
										
										outputStr += ")";
									}
								} else {
									outputStr += rowsArray[i][columns[x]['index']]['value'];
								}
							} else {
								outputStr += "Unknown";
							}
							
							outputStr += '</td>';
						}

						outputStr += '<td>' + 
							'<div class="pull-left"><i id="' + inputRowStruct.attr('id') + '-' + i + '" class="icon-pencil"></i></div>' + 
							'<div class="pull-right"><i id="' + inputRowStruct.attr('id') + '-' + i + '" class="icon-remove"></i></div>' + 
						'</td>';
						
						outputStr += '</tr>';
					}
				}
				
				outputStr += '' + 
					'</tbody>' + 
				'</table>';

				outputDivStruct.html(outputStr);
			}
		}
	}
	</script>
	
	<script type="text/javascript">
	$(document).ready(function(){ 
		$('.modal-footer #arrayModalSaveButton').on('click', function(){
			// Get the modal id 
			var parts = $(this).parent().parent().attr('id').split("_");
			var modalId = parts[1];
			var rowArray = {};
			var failure = false; 
			var failureStr = "";
			
			// Get all of the fields 
			$('input[id^="arrayModal_' + modalId + '_"]').each(function(){
				var id = this.id;

				// Just process the form fields 
				// TODO: Redefine other names so they are not arrayModal_ 
				if(id != "arrayModal_" + modalId + "_optionName" && id != "arrayModal_" + modalId + "_optionValue" && id != "arrayModal_" + modalId + "_arrayOptionString"){
					if($("#" + id).val().length > 0){
						var indx = id.match(/arrayModal_(\d+)_(\w+)/);
						
						if(indx.length == 3){
							// indx[2] is the name (\w+) 
							rowArray[indx[2]] = $("#" + id).val();
						}
						
						$("#" + id).val("");
					} else {
						failureStr = failureStr + "You need to fill in the field " + $("#" + id).attr('placeholder') + "<br />";
						failure = true;
					}
				}
			});
			
			$('select[id^="arrayModal_' + modalId + '_"]').each(function(){
				var id = this.id;
				
				if($("#" + id).val().length > 0){
					var indx = id.match(/arrayModal_(\d+)_(\w+)/);
					
					if(indx.length == 3){
						// indx[2] is the name (\w+) 
						rowArray[indx[2]] = $("#" + id).val();
					}
					
					$("#" + id).val("");
				} else {
					failureStr = failureStr + "You need to fill in the field " + $("#" + id).attr('placeholder') + "<br />";
					failure = true;
				}
			});
			
			// All good... store the value and close the popup 
			if(failure == false){
				// It doesn't matter which operation (add or edit) it is, check if the index exists first and if not create a new entry 
				var rowsXML = $.parseXML($("#inputConfigOption_" + modalId).val());
				var edit = 0;

				if(rowsXML != null){
					var count = 0;
					// Search for the entry 
					$(rowsXML).find("config > configRecord").each(function(){
						// Find the record index 
						$(this).children("configValue").each(function(){
							if($(this).children('index').text() == "index"){
								// Check if the entry still exists 
								if($(this).children('value').text() == rowArray['index']){
									edit = count;
								}
							}
						});

						count++;
					});
				}

				if(edit != 0){
					var count = 0;
					// This is an edit 
					$(rowsXML).find("config > configRecord").each(function(){
						// Find the record index 
						$(this).children("configValue").each(function(){
							if(count == edit){
								$(this).children('value').text(rowArray[$(this).children('index').text()]);
							}
						});

						count++;
					});
				} else {
					// This is adding a new record 
					var newRecord = "	<configRecord>\n";
					$.each(rowArray, function(key, value){
						newRecord += "		<configValue>\n";
						newRecord += "			<index>" + key + "</index>\n";
						newRecord += "			<value>" + value + "</value>\n";

						if(value == "option"){
							newRecord += "<options>" + $("#arrayModal_" + modalId + "_arrayOptionString").val() + "</options>";
						}
						newRecord += "		</configValue>\n";
					});
					newRecord += "	</configRecord>\n";
					console.log(newRecord);
					$(rowsXML).find('config').append($(newRecord));
				}
				
				// Store the result 
				var result = (new XMLSerializer()).serializeToString(rowsXML);
				
				// Remove any xmlns if they exist 
				result = result.replace(/( xmlns\=\"(.+)\")/g, "");

				// Change configrecord back to configRecord 
				result = result.replace(/configrecord/g, "configRecord");
				result = result.replace(/configvalue/g, "configValue");
				
				$('input[name="inputConfigOption[' + modalId + ']"]').val(result);
				
				// Rebuild the table 
				generateArrayTypeDisplay($("#arrayTableHeader_" + modalId), $('input[name="inputConfigOption[' + modalId + ']"]'), $("#arrayTableDisplay_" + modalId));
				
				$('#arrayModal_' + modalId).modal('hide');
			} else {
				// Display the error message 
				$("#arrayModalMessages").html(failureStr).removeClass("hidden");
			}
		});

		$('div[id^="arrayTableDisplay_"]').on('click', '.icon-remove', function(e){
			var id = $(this).attr('id');
			
			if(id !== ""){
				if(!$(this).hasClass("icon-white")){
					bootbox.confirm("Are you sure you want to delete this?", function(result){
						if(result == true){
							// Break apart the id 
							// Format: <divId>-<index> 
							var parts = id.split("-");
							
							if(parts.length == 2){
								// Store the specific variables 
								var divId = parts[0];
								var rowIndex = parts[1];
								
								// Get the contents 
								var rowsXML = $.parseXML($("#" + divId).val());
								
								if(rowsXML != null){
									// Go through each record 
									var count = 0;
									$(rowsXML).find("config > configRecord").each(function(){
										// Find the record index 
										if(count == rowIndex){
											$(this).remove();
										}

										count++;
									});
									
									$("#" + divId).val((new XMLSerializer()).serializeToString(rowsXML));
									
									// Figure out the array id 
									var parts = divId.split("_");
									
									generateArrayTypeDisplay($("#arrayTableHeader_" + parts[1]), $('input[name="inputConfigOption[' + parts[1] + ']"]'), $("#arrayTableDisplay_" + parts[1]));
								}
							}
						}
					}); 
				}
			}
		});

		$('div[id^="arrayTableDisplay_"]').on('click', '.icon-pencil', function(e){
			var id = $(this).attr('id');
			
			if(id !== ""){
				// Break apart the id 
				// Format: <divId>-<index> 
				var parts = id.split("-");
				
				if(parts.length == 2){
					// Get the config option id 
					var configParts = parts[0].split("_");
					
					// Store the specific variables 
					var modalId = configParts[1];
					var divId = parts[0];
					var rowIndex = parts[1];
					
					// Get the contents 
					var rowsXML = $.parseXML($("#" + divId).val());
								
					if(rowsXML != null){
						// Go through each record 
						var count = 0;
						$(rowsXML).find("config > configRecord").each(function(){
							// Find the record index 
							$(this).children("configValue").each(function(){
								if(count == rowIndex){
									$("#arrayModal_" + modalId + "_" + $(this).children("index").text()).val($(this).children("value").text());
								}
							});

							count++;
						});
						
						$('#arrayModal_' + modalId).modal('show');
					}
				}
			}
		});
		
		// This is the handler for arrayModal_ fields change 
		$('select[id^="arrayModal_"]').on('change', function(e){
			var id = $(this).attr('id');
			
			if(id !== ""){
				if($("#" + id).find("option:selected").val() == "option"){
					// Enter the options values 
					// The modal is arrayModal_<number> 
					var parts = id.match(/(arrayModal_(\d+))_(\w+)/);
					console.log(parts[1]);
					var output = "<div><input type=\"text\" name=\"" + parts[1] + "_optionName\" id=\"" + parts[1] + "_optionName\" value=\"\" placeholder=\"Name\" />" + 
							"<input type=\"text\" name=\"" + parts[1] + "_optionValue\" id=\"" + parts[1] + "_optionValue\" value=\"\" placeholder=\"Value\" />" + 
							"<a id=\"" + parts[1] + "_optionAdd\"><i class=\"icon-plus\"></i> Add</a>";

					if(parts != null){
						$("#" + parts[1] + " .modal-body").append(output);
					}
				}
			}
		});

		// Handler for the options add button 
		$('.modal-body').on('click', "a", function(e){
			var id = this.id;
			
			if(id !== ""){
				// The modal is arrayModal_<number> 
				var parts = id.match(/arrayModal_(\d+)_(\w+)/);
				
				if(parts != null){
					var outputStr = "<option>\n<index>" + $("#arrayModal_" + parts[1] + "_optionValue").val() + "</index>\n<displayName>" + $("#arrayModal_" + parts[1] + "_optionName").val() + "</displayName>\n</option>\n";

					$("#arrayModal_" + parts[1] + "_arrayOptionString").val($("#arrayModal_" + parts[1] + "_arrayOptionString").val() + outputStr);
					
					$("#arrayModal_" + parts[1] + "_optionName").val("");
					$("#arrayModal_" + parts[1] + "_optionValue").val("");
				}
			}
		});
	});
	</script>
	<?php 
}

function EditArrayContent($row){
	ImportClass("Xml.XMLParser");
	
	$xml = XMLParser::parse($row->getOptions());
	$inputFieldsArray = $xml['config']['configHeader'];
		
	?>
	<div id="arrayTableDisplay_<?php echo $row->getId(); ?>">
	
	</div>
	<div class="modal fade" id="arrayModal_<?php echo $row->getId(); ?>">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<h3>Item</h3>
		</div>
		<div class="modal-body">
			<div id="arrayModalMessages" class="alert alert-error hidden"></div>
			<?php 
			$columnCount = 1;
			
			foreach($inputFieldsArray as $fieldValue){
				if(count($fieldValue['option']) > 0){
					?>
					<select name="arrayModal_<?php echo $row->getId(); ?>_<?php echo $fieldValue['index']; ?>" id="arrayModal_<?php echo $row->getId(); ?>_<?php echo $fieldValue['index']; ?>" placeholder="<?php echo $fieldValue['displayText']; ?>">
						<option value="">- Select <?php echo $fieldValue['displayText']; ?> -</option>
					<?php 
					foreach($fieldValue['option']['configHeader'] as $record){
						?>
						<option value="<?php echo $record['index']; ?>"><?php echo $record['displayText']; ?></option>
						<?php 
					}
					?>
					</select>
					<?php 
				} else {
					?>
					<input type="text" name="arrayModal_<?php echo $row->getId(); ?>_<?php echo $fieldValue['index']; ?>" id="arrayModal_<?php echo $row->getId(); ?>_<?php echo $fieldValue['index']; ?>" value="" placeholder="<?php echo $fieldValue['displayText']; ?>" />
					<?php 
				}
				
				$columnCount++;
			}
			?>
		</div>
		<div class="modal-footer">
			<a href="#" class="btn" data-dismiss="modal">Close</a>
			<a href="#" class="btn save-button btn-primary" id="arrayModalSaveButton">Save Changes</a>
		</div>
		<input type="hidden" name="arrayModalAction_<?php echo $row->getId(); ?>" id="arrayModalAction_<?php echo $row->getId(); ?>" value="-1" />
		<input type="hidden" name="arrayModal_<?php echo $row->getId(); ?>_arrayOptionString" id="arrayModal_<?php echo $row->getId(); ?>_arrayOptionString" value="" placeholder="test" />
	</div>
	<a href="#arrayModal_<?php echo $row->getId(); ?>" id="addArrayItem" data-toggle="modal"><i class="icon-plus"></i> Add</a>
	<input type="hidden" name="arrayTableHeader_<?php echo $row->getId(); ?>" id="arrayTableHeader_<?php echo $row->getId(); ?>" value="<?php echo $row->getOptions(); ?>" />
	<input type="hidden" name="inputConfigOption[<?php echo $row->getId(); ?>]" id="inputConfigOption_<?php echo $row->getId(); ?>" value="<?php echo $row->getValue(); ?>" />
	<?php 
}

?>
