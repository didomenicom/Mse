<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

function Add(){
	if(UserFunctions::hasComponentAccess("media", "add") == true){
		
		$result = (Url::getParts('result') != NULL ? Url::getParts('result') : 0);
		$id = (strlen(Url::getParts('id')) > 0 ? Url::getParts('id') : "");
		
		if($result == 0){
			// Display the page text
			echo Text::pageTitle("Add File");
			?>
			<link rel="stylesheet" href="<?php echo Url::getAdminHttpBase('template'); ?>/mousewareAdmin/css/jquery.fileupload.css">
			<script src="<?php echo Url::getAdminHttpBase('template'); ?>/mousewareAdmin/js/jquery.fileupload.js"></script>
			<script src="<?php echo Url::getAdminHttpBase('template'); ?>/mousewareAdmin/js/jquery.fileupload-process.js"></script>
			<script src="<?php echo Url::getAdminHttpBase('template'); ?>/mousewareAdmin/js/jquery.fileupload-validate.js"></script>
			<script type="text/javascript">
			$(document).ready(function(){
				$('#fileupload').fileupload({
					url: "<?php echo Url::getAdminHttpBase(); ?>/index.php?ajaxRequest=1",
					type: 'POST', 
					dataType: 'json',
					autoUpload: true,
					formData: {
					id: "pJGnkvIcHZQVJyv7P7DaN9epmND/0tFgPAorrc6Dy1rroUYuy+rri2E4oYReD3ypHflffP8HU6NLHDlZQbKLU/Y6ANNIZ30ndN+wmuzI4hkdc/gU+HESapmKTdsTMy75AR7Jm2Uamu3SOzQjVKhpRCV6w0HPtIhz3HtvKeMKjottYnZklMyrPp5gm6kupAEhBeVAA+tQRi/wD+P63ZRU66iVocA64N27w0ZXGcVpVMg=", 
					task: "upload"},
				})
				.on('fileuploadadd', function(e, data){
					$.each(data.files, function(index, file){
						var fileNameText = $('<span />').text(file.name);
						var node = $('<p />').append(fileNameText);
						node.fileNameText = fileNameText;
						
						if(!index){
							var progressBarInner = $('<div />').addClass('progress-bar progress-bar-info');
							var progressBar = $('<div />').addClass('progress progress-striped').html(progressBarInner);
							node.append('<br>').append(progressBar);
							node.progressBar = progressBar;
						}

						node.appendTo($('#uploadFileList'));
						file.node = node;
					});
				})
				.on('fileuploadprogress', function(e, data){
					data.files[0].node.progressBar.children().css('width', parseInt(data.loaded / data.total * 100, 10) + '%');
				})
				.on('fileuploadprogressall', function(e, data){
					$('#progress .progress-bar').css('width', parseInt(data.loaded / data.total * 100, 10) + '%');
				})
				.on('fileuploaddone', function(e, data){
					if(data._response.result == "1"){
						data.files[0].node.fileNameText.append("... <b>Success</b>");
					} else {
						data.files[0].node.progressBar.children().removeClass('progress-bar-info').addClass('progress-bar-danger');
						data.files[0].node.fileNameText.append("... <b>Failure</b>");
					}
				});
			});
			</script>
			<div id="formMessages" class="alert alert-danger hidden"></div>
			<span class="btn btn-success fileinput-button">
			<i class="glyphicon glyphicon-plus"></i>
			<span>Add files...</span>
			<input id="fileupload" type="file" name="files[]" multiple="multiple">
			</span>
			<br />
			<br />
			<div id="progress" class="progress progress-striped">
				<div class="progress-bar progress-bar-success"></div>
			</div>
			<div id="uploadFileList"></div>
			<div class="controls">
				<div class="form-actions">
					<a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=media&act=manage" class="btn btn-default">Close</a>
				</div>
			</div>
			<?php
		}
		
		if($result == 1){
			$info = Form::getParts();
			$error = false;
			$errorMessage = "";
			
			if(!isset($info['inputDisplayName']) || $info['inputDisplayName'] === ""){
				$error = true;
				$errorMessage = "Please enter a name";
			}
			
			if($error == false){
				// Save it
				$data->setDisplayName($info['inputDisplayName']);
				$data->setFunctions($info['inputFunctions']['value']);
				$data->setDefaultUrl($info['inputDefaultUrl']);
				$data->setActive($info['inputActive']);
				
				if($data->save() == true){
					if(strlen($id) > 0){
						Messages::setMessage("Component Saved", Define::get("MessageLevelSuccess"));
					} else {
						Messages::setMessage("Component Added", Define::get("MessageLevelSuccess"));
					}
					
					Url::redirect(Url::getAdminHttpBase() . "/index.php?option=media&act=manage", 0, false);
				}
			} else {
				Messages::setMessage($errorMessage, Define::get("MessageLevelError"));
				Url::redirect(Url::getAdminHttpBase() . "/index.php?option=media&act=" . (Url::getParts('act') === "edit" ? "edit" . ($id > 0 ? "&id=" . $id : "") : "add"), 0, false);
			}
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(Url::home(), 3, false);
	}
}

?>