<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

function Edit(){
	if(UserFunctions::hasComponentAccess("shortlink", "edit") == true){
		ImportClass("Shortlink.ShortLink");
		
		$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
		$id = (strlen(Url::getParts('id')) > 0 ? Url::getParts('id') : "");
		
		// Create the class
		$data = ShortLink::get($id);
		
		if($result == 0){
			// Display the page text
			echo Text::pageTitle((Url::getParts('act') === "edit" ? "Edit" : "Add") . " Short Link");
			?>
			<script type="text/javascript">
			$(document).ready(function(){
				$("#adminForm").submit(function(e){
					if($("#inputRedirectUrl").val() == ""){
						$("#formMessages").html("You need to enter a url").removeClass("hidden");
						e.preventDefault();
					}
				});
			});
			</script>
			<div id="formMessages" class="alert alert-danger hidden"></div>
			<form name="adminForm" id="adminForm" method="post" action="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=shortlink&act=<?php echo (Url::getParts('act') === "edit" ? "edit" . (strlen($id) > 0 ? "&id=" . $id : "") : "add"); ?>&result=1">
				<div class="form-group">
					<label class="control-label" for="inputRedirectUrl">Redirect URL</label>
					<div class="controls">
						<input type="text" name="inputRedirectUrl" id="inputRedirectUrl" value="<?php echo (strlen($id) > 0 ? $data->redirectLink : ""); ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label" for="inputInternal">Internal Link</label>
					<div class="controls">
						<select name="inputInternal" id="inputInternal">
							<option value="0"<?php echo (strlen($id) > 0 ? ($data->internal == 0 ? "selected=\"selected\"" : "") : "selected=\"selected\""); ?>>No</option>
							<option value="1"<?php echo (strlen($id) > 0 && $data->internal == 1 ? "selected=\"selected\"" : ""); ?>>Yes</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label" for="inputActive">Active</label>
					<div class="controls">
						<select name="inputActive" id="inputActive">
							<option value="0"<?php echo (strlen($id) > 0 && $data->active == 0 ? "selected=\"selected\"" : ""); ?>>No</option>
							<option value="1"<?php echo (strlen($id) > 0 ? ($data->active == 1 ? "selected=\"selected\"" : "") : "selected=\"selected\""); ?>>Yes</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label" for="inputExpirationDate">Expiration Date</label>
					<div class="controls">
						<div class="input-append date" data-date="<?php echo date("m/d/Y H:i:s"); ?>" data-date-format="mm/dd/yyyy hh:mm:ss">
							<input type="text" name="inputExpirationDate" id="inputExpirationDate" placeholder="<?php echo "0000-00-00 00:00:00"; ?>" value="<?php echo (strlen($id) > 0 ? $data->expirationDate : "0000-00-00 00:00:00"); ?>" />
						</div>
					</div>
				</div>
				<div class="controls">
					<div class="form-actions">
						<button type="submit" class="btn btn-primary">Save changes</button>
						<a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=shortlink&act=manage" class="btn btn-default">Cancel</a>
					</div>
				</div>
			</form>
			<?php
		}
		
		if($result == 1){
			$info = Form::getParts();
			$error = false;
			$errorMessage = "";
			
			if(!isset($info['inputRedirectUrl']) || $info['inputRedirectUrl'] === ""){
				$error = true;
				$errorMessage = "Please enter a url";
			}
			
			if($error == false){
				// Save it
				if(strlen($id) > 0){
					if(ShortLink::update($id, $info['inputRedirectUrl'], $info['inputInternal'], $info['inputActive'], $info['inputExpirationDate']) == true){
						Messages::setMessage("Short Link Saved", Define::get("MessageLevelSuccess"));
					} else {
						Messages::setMessage("Short Link NOT Saved", Define::get("MessageLevelError"));
					}
				} else {
					if(ShortLink::add($info['inputRedirectUrl'], $info['inputInternal'], $info['inputActive'], $info['inputExpirationDate']) == true){
						Messages::setMessage("Short Link Added", Define::get("MessageLevelSuccess"));
					} else {
						Messages::setMessage("Short Link NOT Added", Define::get("MessageLevelError"));
					}
				}
				
				Url::redirect(Url::getAdminHttpBase() . "/index.php?option=shortlink&act=manage", 0, false);
			} else {
				Messages::setMessage($errorMessage, Define::get("MessageLevelError"));
				Url::redirect(Url::getAdminHttpBase() . "/index.php?option=shortlink&act=" . (Url::getParts('act') === "edit" ? "edit" . ($id > 0 ? "&id=" . $id : "") : "add"), 0, false);
			}
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(Url::home(), 3, false);
	}
}

?>