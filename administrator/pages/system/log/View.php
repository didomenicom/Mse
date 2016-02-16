<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2013 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

/**
 * TODO: Add a Wiki style table of contents for large documentation files
 */
function View(){
	if(UserFunctions::hasComponentAccess("log", "view") == true){
		ImportClass("Log.SysLogs");
		
		// Display the page text
		echo Text::pageTitle("View Logs");
		
		// Figure out the actions
		?>
		<div class="btn-toolbar pull-right" style="margin-top: 0px;">
			<div class="btn-group">
				<a class="btn" href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=groups&act=help"><i class="icon-question-sign"></i></a>
			</div>
		</div>
		<?php
		
		// Create the class
		$filter['level'] = 6;
		$items = new SysLogs($filter);
		?>
		<form name="adminForm" method="post" action="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=log&act=view">
			<table align="center" cellpadding="0" cellspacing="0" border="0" width="550">
				<tr>
					<td>&nbsp;
						
					</td>
				</tr>
			</table>
			<?php
			if($items->rowsExist()){
			?>
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th width="10%">
								User
							</th>
							<th width="10%">
								Location
							</th>
							<th width="10%">
								Level
							</th>
							<th width="55%">
								Message
							</th>
							<th width="15%">
								Timestamp
							</th>
						</tr>
					</thead>
				<?php	
				$cnt = 1;
				while($items->hasNext()){
					$row = $items->getNext();
					?>
					<tr>
						<td>
							<?php echo $row->getUid(1); ?>
						</td>
						<td>
							<?php echo $row->getLocation(1); ?>
						</td>
						<td>
							<?php echo $row->getLevel(1); ?>
						</td>
						<td>
							<?php echo substr($row->getMessage(1), 0, 150); ?>
						</td>
						<td>
							<?php echo $row->getTimestamp(); ?>
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
		?>
		</form>
		<?php
	} else {
		Messages::setMessage("Permission Denied", Define::get("MessageLevelError"));
		Url::redirect(Url::home(), 3, false);
	}
}

?>
