<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function EditDoubleHeader(){
	?>
	<script type="text/javascript">
	$(document).ready(function(){ 
		$(".inputDouble").change(function(e){
			// TODO: Check for decimalLength 
			var doubleTest = /^(([0-9]+\.[0-9]+)|([0-9]+))$/;

			if(doubleTest.test($(this).val())){
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

function EditDoubleContent($row){
	// See if there is a MaxLength parameter
	$maxLength = 0;
	
	if(strlen($row->getOptions()) > 0){
		preg_match('/MaxLength=(\d+)/', $row->getOptions(), $matches);
		$maxLength = $matches[1];
	}
	
	// See if there is a DecimalLength parameter
	$decimalLength = 0;
	
	if(strlen($row->getOptions()) > 0){
		preg_match('/DecimalLength=(\d+)/', $row->getOptions(), $matches);
		$decimalLength = $matches[1];
	}
	?>
	<input type="text" class="inputDouble" name="inputConfigOption[<?php echo $row->getId(); ?>]" id="inputConfigOption[<?php echo $row->getId(); ?>]"<?php echo ($maxLength > 0 ? ' maxlength="' . ($decimalLength > 0 ? ($maxLength + $decimalLength + 1) :  $maxLength) . '"' : '') . ($decimalLength > 0 ? ' decimallength="' . $decimalLength . '"' : ''); ?> value="<?php echo $row->getValue(); ?>" />
	<?php 
}

?>
