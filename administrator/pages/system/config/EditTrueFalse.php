<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function EditTrueFalseHeader(){
	// No header needs to be displayed
}

function EditTrueFalseContent($row){
	?>
	<div class="input-append">
		<select name="inputConfigOption[<?php echo $row->getId(); ?>]" id="inputConfigOption[<?php echo $row->getId(); ?>]">
			<?php 
			if($row->getOptions(1) === "Y/N"){
				?>
			<option value="1"<?php echo ($row->getValue() == 1 ? " selected=\"selected\"" : ""); ?>>Yes</option>
			<option value="0"<?php echo ($row->getValue() == 0 ? " selected=\"selected\"" : ""); ?>>No</option>
				<?php 
			}
			
			if($row->getOptions(1) === "O/O"){
				?>
			<option value="1"<?php echo ($row->getValue() == 1 ? " selected=\"selected\"" : ""); ?>>On</option>
			<option value="0"<?php echo ($row->getValue() == 0 ? " selected=\"selected\"" : ""); ?>>Off</option>
				<?php 
			}
			
			if($row->getOptions(1) === "T/F"){
				?>
			<option value="1"<?php echo ($row->getValue() == 1 ? " selected=\"selected\"" : ""); ?>>True</option>
			<option value="0"<?php echo ($row->getValue() == 0 ? " selected=\"selected\"" : ""); ?>>False</option>
				<?php 
			}
			?>
		</select>
	</div>
	<?php 
}

?>
