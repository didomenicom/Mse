<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function Manage(){
	if(UserFunctions::getLoggedIn() != NULL && true == true){ // TODO: Add permission group
		// Grab the class to walk through the DB
		ImportClass("Help.Helps");
		
		// Display the page text
		echo Text::pageTitle("Manage Help Menus");
		
		// Figure out the actions
		?>
		<div class="btn-toolbar pull-right" style="margin-top: 0px;">
			<div class="btn-group">
				<a class="btn" href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=help&task=add"><i class="icon-plus"></i></a>
			</div>
		</div>
		<?php
		
		// Create the class
		$items = new Helps();
		
		?>
		<form name="adminForm" method="post" action="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=help&task=manage">
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
							<th width="5%">
								
							</th>
							<th width="45%">
								Component
							</th>
							<th width="45%">
								Title
							</th>
							<th width="5%">
								ID
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
							<ul class="nav" style="margin-top: 0px; margin-bottom: 0px;">
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" style="margin-top: 0px; margin-bottom: 0px;"><?php echo $cnt; ?></a>
									<ul class="dropdown-menu">
										<li><a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=help&task=edit&id=<?php echo $row->getId(); ?>">Edit</a></li>
										<li><a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=help&task=delete&id=<?php echo $row->getId(); ?>">Delete</a></li>
									</ul>
								</li>
							</ul>
						</td>
						<td>
							<?php echo $row->getComponent(1); ?>
						</td>
						<td>
							<?php echo $row->getTitle(1); ?>
						</td>
						<td>
							<?php echo $row->getId(); ?>
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
