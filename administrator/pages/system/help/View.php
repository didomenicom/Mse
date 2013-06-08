<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

/**
 * TODO: Add a Wiki style table of contents for large documentation files
 */
function View(){
	if(UserFunctions::getLoggedIn() != NULL && true == true){
		$com = (Url::getParts('com') !== "" ? Url::getParts('com') : NULL);
		$para = (Url::getParts('para') !== "" ? Url::getParts('para') : NULL);
		
		if($com != NULL){
			ImportClass("Component.Component");
			ImportClass("Help.Helps");
			
			// Create the component class
			$component = new Component($com);
			
			// Get all of the options
			$filter['component'] = $component->getId();
			
			if($para != NULL){
				$filter['id'] = $para;
			}
			
			$helps = new Helps($filter);
			
			// Display the page text
			echo Text::pageTitle($component->getName(1) . " Help");
			?>
			<script type="text/javascript">
			function back(){
				window.location.replace("<?php echo Url::getAdminHttpBase(); ?>/index.php?option=help"); // TODO: 
			}
			</script>
			<div id="formMessages" class="alert alert-error hidden"></div>
			<?php 
			if($para != NULL){
			?>
			<div class="text-right"><a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=help&com=<?php echo $com; ?>">View all</a></div>
			<?php 
			}
			?>
			<?php 
			// Display all of the config options
			if($helps->rowsExist()){
				while($helps->hasNext()){
					$row = $helps->getNext();
					
					?>
					<h3><a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=help&com=<?php echo $com; ?>&para=<?php echo $row->getId(); ?>"><?php echo $row->getTitle(1); ?></a></h3>
					<p><?php echo $row->getContent(1); ?></p>
					
					<?php
					if($helps->hasNext()){
						?>
					<hr class="bs-docs-separator">
						<?php 
					}
				}
			} else {
				?>
				<p>No documentation exists</p>
				<?php 
			}
			
			?>
			<div class="controls">
				<div class="form-actions">
					<button type="button" class="btn" onClick="back();">Return</button>
				</div>
			</div>
			<?php
		} else {
			// No component specified
			// TODO: Error
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(UserFunctions::getLoginUrl(), 0, false);
	}
}

?>