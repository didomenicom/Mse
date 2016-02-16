<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function EditTextAreaHeader(){
	
}

function EditTextAreaContent($row){
	?>
	<textarea name="inputConfigOption[<?php echo $row->getId(); ?>]" id="inputConfigOption[<?php echo $row->getId(); ?>]" rows="8" class="field span12"><?php echo $row->getValue(); ?></textarea>
	<?php 
}

?>
