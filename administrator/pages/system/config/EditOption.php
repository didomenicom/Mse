<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function EditOptionHeader(){
	
}

function EditOptionContent($row){
	if($row->getType() == 7){ // Option
		// Break the options array apart ('|' is the deliminator)
		$optionsArray = explode("|", $row->getOptions());
		$options = array();
		
		// Break apart the name and display name (Format: DisplayName(name))
		for($i = 0; $i < count($optionsArray); $i++){
			if(strlen($optionsArray[$i]) > 0){
				// Extract the name/value
				preg_match('/([\w\s]+)\((\w+)\)/', $optionsArray[$i], $matches);
				
				$options[$i]['name'] = $matches[1];
				$options[$i]['value'] = $matches[2];
			}
		}
		?>
		<select name="inputConfigOption[<?php echo $row->getId(); ?>]" id="inputConfigOption[<?php echo $row->getId(); ?>]">
			<?php
			// List all of the options
			for($i = 0; $i < count($options); $i++){
				?>
			<option value="<?php echo $options[$i]['value']; ?>"<?php echo ($row->getValue() == $options[$i]['value'] ? "selected=\"selected\"" : ""); ?>><?php echo $options[$i]['name']; ?></option>
				<?php
			}
			?>
		</select>
		<?php 
	}
}

?>
