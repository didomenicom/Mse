<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

function Delete(){
	if(UserFunctions::getLoggedIn() != NULL && true == true){
		ImportClass("AjaxHandler.AjaxHandler");
		
		$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
		$id = (Url::getParts('id') > 0 ? Url::getParts('id') : 0);
		
		if($id > 0){
			// Create the class
			$data = new AjaxHandler($id);
			
			if($data->canDelete() == true){
				if($result == 0){
					// Display the page text
					echo Text::pageTitle("Delete Ajax Handler");
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
						window.location.replace("<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=ajaxHandler&task=manage");
					}
					</script>
					<form name="adminForm" id="adminForm" method="post" action="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=ajaxHandler&task=delete&id=<?php echo $id; ?>&result=1">
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
						Messages::setMessage("Ajax Handler Deleted", Define::get("MessageLevelSuccess"));
					}
					
					Url::redirect(Url::getAdminHttpBase() . "/index.php?option=development&act=ajaxHandler&task=manage", 0, false);
				}
			} else {
				Messages::setMessage("Cannot delete ajax handler", Define::get("MessageLevelError"));
				Url::redirect(Url::getAdminHttpBase() . "/index.php?option=development&act=ajaxHandler&task=manage", 0, false);
			}
		} else {
			Messages::setMessage("An unknown error has occured", Define::get("MessageLevelError"));
			Url::redirect(Url::getAdminHttpBase() . "/index.php?option=development&act=ajaxHandler&task=manage", 0, false);
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(UserFunctions::getLoginUrl(), 0, false);
	}
}

?>