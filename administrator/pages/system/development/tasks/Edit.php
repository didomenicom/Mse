<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

function Edit(){
	if(UserFunctions::getLoggedIn() != NULL && true == true){
		ImportClass("Development.Tasks.Tasks");
		ImportClass("Development.Tasks.Task");
		
		$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
		$id = (Url::getParts('id') > 0 ? Url::getParts('id') : 0);
		
		// Create the class
		$data = new Task($id);
		
		if($result == 0){
			// Display the page text
			echo Text::pageTitle((Url::getParts('task') === "edit" ? "Edit" : "Add") . " Task");
			?>
			<script type="text/javascript">
			$(document).ready(function(){
				$("#adminForm").submit(function(e){
					if($("#inputName").val() == ""){
						$("#formMessages").html("You need to enter a name").removeClass("hidden");
						e.preventDefault();
					}
				});
			});
			</script>
			<div id="formMessages" class="alert alert-danger hidden"></div>
			<form name="adminForm" id="adminForm" method="post" action="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=tasks&task=<?php echo (Url::getParts('task') === "edit" ? "edit" . ($id > 0 ? "&id=" . $id : "") : "add"); ?>&result=1">
				<div class="row">
					<div class="form-group col-sm-5">
						<label for="inputName">Name</label>
						<input type="text" class="form-control" name="inputName" id="inputName" value="<?php echo ($id > 0 ? $data->getName(1) : ""); ?>" />
					</div>
				</div>
				<div class="row">
					<div class="form-group col-sm-5">
						<label for="inputDescription">Description</label>
						<textarea name="inputDescription" id="inputDescription" rows="8" class="field col-md-12"><?php echo ($id > 0 ? $data->getDescription(1) : ""); ?></textarea>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-sm-5">
						<button type="submit" class="btn btn-primary">Save changes</button>
						<a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=tasks&task=manage" class="btn btn-default">Cancel</a>
					</div>
				</div>
			</form>
			<?php
		}
		
		if($result == 1){
			$info = Form::getParts();
			$error = false;
			$errorMessage = "";
			
			if(!isset($info['inputName']) || $info['inputName'] === ""){
				$error = true;
				$errorMessage = "Please enter a name";
			}
			
			if($error == false){
				// Save it
				$data->setName($info['inputName']);
				$data->setDescription($info['inputDescription']);
				
				if($id == 0){
					$data->setVerifyBy(UserFunctions::getLoggedIn()->getId());
				}
				
				if($data->save() == true){
					if($id > 0){
						Messages::setMessage("Task Saved", Define::get("MessageLevelSuccess"));
					} else {
						Messages::setMessage("Task Added", Define::get("MessageLevelSuccess"));
					}
					
					Url::redirect(Url::getAdminHttpBase() . "/index.php?option=development&act=tasks&task=manage", 0, false);
				}
			} else {
				Messages::setMessage($errorMessage, Define::get("MessageLevelError"));
				Url::redirect(Url::getAdminHttpBase() . "/index.php?option=development&act=tasks&task=" . (Url::getParts('act') === "edit" ? "edit" . ($id > 0 ? "&id=" . $id : "") : "add"), 0, false);
			}
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(Url::home(), 3, false);
	}
}

?>