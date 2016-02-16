<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function EditDateHeader(){
	?>
	<script type="text/javascript">
	$(document).ready(function(){ 
		$(".inputDate").change(function(e){
			// TODO: Check for format 
			var dateTest = /^((0?[1-9]|1[012])[- \/.](0?[1-9]|[12][0-9]|3[01])[- \/.](19|20)?[0-9]{2})*$/; // TODO: Handle no character between numbers 

			if(dateTest.test($(this).val())){
				if($(this).parentsUntil("control-group").hasClass("error")){
					$(this).parentsUntil("control-group").removeClass("error");
				}
			} else {
				$(this).parentsUntil("control-group").addClass("error");
			}
		});
	});
	</script>
	<?php 
}

function EditDateContent($row){
	?>
	<div class="input-append">
		<input type="text" class="inputDate" name="inputConfigOption[<?php echo $row->getId(); ?>]" id="inputConfigOption_<?php echo $row->getId(); ?>"<?php echo ($row->getOptions() !== "" ? ' format="' . $row->getOptions() . '"' : ''); ?> value="<?php echo $row->getValue(); ?>" />
		<span class="add-on"><i class="icon-calendar"></i></span>
	</div>
	<?php 
}

?>
