<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function Manage(){
	if(UserFunctions::getLoggedIn() != NULL && true == true){ // TODO: Add permission check
		// Grab the class to walk through the DB
		ImportClass("AjaxHandler.AjaxHandlers");
		
		// Display the page text
		echo Text::pageTitle("Manage Ajax Handlers");
		
		// TODO: Add view expired
		?>
		<div class="btn-toolbar pull-right" style="margin-top: 0px;">
			<div class="btn-group">
				<a class="btn" href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=ajaxHandler&task=add"><i class="icon-plus"></i></a>
				<a class="btn" href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=config&act=manage&com=ajaxHandler"><i class="icon-wrench"></i></a>
				<a class="btn" href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=help&act=manage&com=ajaxHandler"><i class="icon-question-sign"></i></a>
			</div>
		</div>
		<?php
		
		// Create the class
		$items = new AjaxHandlers();
		
		?>
		<form name="adminForm" method="post" action="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=ajaxHandler&task=manage">
			<?php
			if($items->rowsExist()){
			?>
			<table class="table table-bordered table-striped table-hover">
				<thead>
					<tr>
						<th width="5%">
							
						</th>
						<th width="28%">
							Handler Name
						</th>
						<th width="22%">
							Class Name
						</th>
						<th width="22%">
							Caller Function
						</th>
						<th width="12%">
							Created
						</th>
						<th width="12%">
							Expires
						</th>
						<th width="5%">
							Details
						</th>
					</tr>
				</thead>
				<tbody>
				<?php	
				$cnt = 1;
				while($items->hasNext()){
					$row = $items->getNext();
					?>
					<tr>
						<td>
							<ul class="nav" style="margin-top: 0px; margin-bottom: 0px;">
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" style="margin-top: 0px; margin-bottom: 0px;"><?php echo $cnt; ?></a>
									<ul class="dropdown-menu">
										<li><a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=ajaxHandler&task=edit&id=<?php echo $row->getId(); ?>"><i class="icon-pencil"></i> Edit</a></li>
										<li><a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=ajaxHandler&task=delete&id=<?php echo $row->getId(); ?>"><i class="icon-trash"></i> Delete</a></li>
									</ul>
								</li>
							</ul>
						</td>
						<td>
							<?php echo $row->getName(1); ?>
						</td>
						<td>
							<?php echo $row->getClassName(1); ?>
						</td>
						<td>
							<?php echo $row->getCallerFunction(1); ?>
						</td>
						<td>
							<?php echo $row->getCreateTimestamp(1); ?>
						</td>
						<td>
							<?php echo ($row->getExpireTimestamp() > 0 ? $row->getExpireTimestamp(1) : "Never"); ?>
						</td>
						<td>
							<a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=ajaxHandler&task=details&id=<?php echo $row->getId(); ?>">Details</a>
						</td>
					</tr>
					<?php
					$cnt++;
				}
				?>
					</tbody>
				</table>
				<?php
			} else {
				?>
				No Results to display
				<?php
			}
			?>
		</form>
		<?php
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(Url::home(), 3, false);
	}
}

?>
