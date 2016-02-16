<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

/**
 * TODO: Move to a global class like help
 */
function Details(){
	if(UserFunctions::hasComponentAccess("users", "details") == true){
		ImportClass("User.User");
		
		$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
		$id = (Url::getParts('id') > 0 ? Url::getParts('id') : 0);
		
		if($id > 0){
			// Create the class
			$data = new User($id);
			
			echo Text::pageTitle("User");
			
			?>
			<script type="text/javascript">
			function cancel(){
				window.location.replace("<?php echo Url::getAdminHttpBase(); ?>/index.php?option=users&act=manage");
			}
			</script>
			<div class="btn-toolbar pull-right" style="margin-top: 0px;">
				<div class="btn-group">
					<a class="btn" href="#"><i class="icon-print"></i></a>
				</div>
			</div>
			<?php echo $data->display(); ?>
			<div class="controls">
				<div class="form-actions">
					<button type="button" class="btn btn-primary" onClick="return cancel();">Close</button>
				</div>
			</div>
			<?php
		} else {
			Messages::setMessage("An unknown error has occurred", Define::get("MessageLevelError"));
			Url::redirect(Url::getAdminHttpBase() . "/index.php?option=user&act=manage", 0, false);
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(Url::home(), 3, false);
	}
}

?>
