<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

function Manage(){
	if(UserFunctions::hasComponentAccess("media", "manage") == true){
		// Grab the class to walk through the DB
		ImportClass("Media.MediaList");
		
		// Create the class
		$items = new MediaList();
		
		echo Text::pageTitle("Manage Media"); 
		
		?>
		<div class="buttonMenu toolbar pull-right">
			<div class="btn-group">
				<a class="btn btn-sm btn-default" href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=media&act=add"><i class="fa fa-plus"></i></a>
				<a class="btn btn-sm btn-default" href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=help&com=media"><i class="fa fa-question-circle"></i></a>
			</div>
		</div>
		<?php
		if($items->rowsExist()){
		?>
			<table class="table table-bordered table-striped table-hover table-condensed table-responsive">
				<thead>
					<tr>
						<th width="5%">
							
						</th>
						<th width="30%">
							Name
						</th>
						<th width="10%">
							Size
						</th>
						<th width="20%">
							Last Modified
						</th>
						<th width="20%">
							Type
						</th>
						<th width="15%">
							Permission
						</th>
					</tr>
				</thead>
			<?php	
			$cnt = 1;
			while($items->hasNext()){
				$row = $items->getNext();
				?>
				<tr>
					<td align="center">
						<?php echo (File::isDirectory($row) == true ? "<i class=\"glyphicon glyphicon-folder-open\"></i>" : "<i class=\"glyphicon glyphicon-file\"></i>"); ?>
					</td>
					<td>
						<?php echo File::getFilename($row); ?>
					</td>
					<td>
						<?php echo File::getSize($row, "K") . "K"; ?>
					</td>
					<td>
						<?php echo File::getLastModified($row); ?>
					</td>
					<td>
						<?php echo (File::getExtension($row) != NULL ? File::getExtension($row) : "Unknown"); ?>
					</td>
					<td>
						<?php echo File::getPermissions($row); ?>
					</td>
				</tr>
				<?php
				$cnt++;
			}
			?>
			</table>
			<?php
		} else {
			?>
			No Results to display
			<?php
		}
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(Url::home(), 3, false);
	}
}

?>