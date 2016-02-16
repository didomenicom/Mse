<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function Delete(){
	if(UserFunctions::hasComponentAccess("groups", "delete") == true){
		ImportClass("Group.Group");
		
		$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
		$id = (Url::getParts('id') > 0 ? Url::getParts('id') : 0);
		
		if($id > 0){
			// Create the class
			$data = new Group($id);
			
			if($data->canDelete() == true){
				if($result == 0){
					// Display the page text
					echo Text::pageTitle("Delete Group");
					?>
					<script type="text/javascript">
					function confirmDelete(){
						bootbox.confirm("Are you sure you want to delete this?", function(result){
							if(result == true){
								$("#adminForm").submit();
							}
						}); 
						
						return false;
					}
					
					function cancel(){
						window.location.replace("<?php echo Url::getAdminHttpBase(); ?>/index.php?option=groups&act=manage");
					}
					</script>
					<form name="adminForm" id="adminForm" method="post" action="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=groups&act=delete&id=<?php echo $id; ?>&result=1">
						<?php echo $data->display(); ?>
						<div class="controls">
							<div class="form-actions">
								<button type="submit" class="btn btn-primary" onClick="return confirmDelete();">Delete</button>
								<button type="button" class="btn" onClick="return cancel();">Cancel</button>
							</div>
						</div>
					</form>
					<?php
				}
				
				if($result == 1){
					$info = Form::getParts();
					
					if($data->delete() == true){
						Messages::setMessage("Group Deleted", Define::get("MessageLevelSuccess"));
					}
					
					Url::redirect(Url::getAdminHttpBase() . "/index.php?option=groups&act=manage", 0, false);
				}
			} else {
				Messages::setMessage("Cannot delete group", Define::get("MessageLevelError"));
				Url::redirect(Url::getAdminHttpBase() . "/index.php?option=groups&act=manage", 0, false);
			}
		} else {
			Messages::setMessage("An unknown error has occurred", Define::get("MessageLevelError"));
			Url::redirect(Url::getAdminHttpBase() . "/index.php?option=groups&act=manage", 0, false);
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(Url::home(), 3, false);
	}
}

?>
