<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function EditTextBoxHeader(){
	
}

function EditTextBoxContent($row){
	// See if there is a MaxLength parameter
	$maxLength = 0;
	
	if(strlen($row->getOptions()) > 0){
		preg_match('/MaxLength=(\d+)/', $row->getOptions(), $matches);
		
		if(count($matches) > 0){
			$maxLength = $matches[1];
		}
	}
	?>
	<input type="text" class="field span12" name="inputConfigOption[<?php echo $row->getId(); ?>]" id="inputConfigOption[<?php echo $row->getId(); ?>]"<?php echo ($maxLength > 0 ? ' maxlength="' . $maxLength . '"' : ''); ?> value="<?php echo $row->getValue(); ?>" />
	<?php 
}

?>
