<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function EditIntegerHeader(){
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
	});
	</script>
	<?php

}

function EditIntegerContent($row){
	// See if there is a MaxLength parameter
	$maxLength = 0;
	
	if(strlen($row->getOptions()) > 0){
		preg_match('/MaxLength=(\d+)/', $row->getOptions(), $matches);
		$maxLength = $matches[1];
	}
	?>
	<input type="text" class="inputInteger field span12" name="inputConfigOption[<?php echo $row->getId(); ?>]" id="inputConfigOption[<?php echo $row->getId(); ?>]"<?php echo ($maxLength > 0 ? ' maxlength="' . $maxLength . '"' : ''); ?> value="<?php echo $row->getValue(); ?>" />
	<?php 
}

?>
