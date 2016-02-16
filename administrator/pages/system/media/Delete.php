<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

function Delete(){
	if(UserFunctions::hasComponentAccess("component", "delete") == true){
		ImportClass("Component.Component");
		
		$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
		$id = (strlen(Url::getParts('id')) > 0 ? Url::getParts('id') : "");
		
		if(strlen($id) > 0){
			// Create the class
			$data = new Component($id);
			
			if($data->canDelete() == true){
				if($result == 0){
					// Display the page text
					echo Text::pageTitle("Delete Component");
					?>
					<script type="text/javascript">
					$(document).ready(function(){ 
						$("#adminForm").submit(function(e){
							bootbox.confirm("Are you sure you want to delete this?", function(result){
								if(result == true){
									$("#adminForm").submit();
								}
							}); 
							
							e.preventDefault();
						});
					});
					</script>
					<form name="adminForm" id="adminForm" method="post" action="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=component&act=delete&id=<?php echo $id; ?>&result=1">
						<div style="padding:10px;">
							<?php echo $data->display(); ?>
						</div>
						<div class="row">
							<div class="form-group col-sm-5">
								<button type="submit" class="btn btn-primary">Delete</button>
								<a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=component&act=manage" class="btn btn-default">Cancel</a>
							</div>
						</div>
					</form>
					<?php
				}
				
				if($result == 1){
					$info = Form::getParts();
					
					if($data->delete() == true){
						Messages::setMessage("Component Deleted", Define::get("MessageLevelSuccess"));
					}
					
					Url::redirect(Url::getAdminHttpBase() . "/index.php?option=component&act=manage", 0, false);
				}
			} else {
				Messages::setMessage("Cannot delete component", Define::get("MessageLevelError"));
				Url::redirect(Url::getAdminHttpBase() . "/index.php?option=component&act=manage", 0, false);
			}
		} else {
			Messages::setMessage("An unknown error has occurred", Define::get("MessageLevelError"));
			Url::redirect(Url::getAdminHttpBase() . "/index.php?option=component&act=manage", 0, false);
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(Url::home(), 3, false);
	}
}

?>