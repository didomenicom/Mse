<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

function Manage(){
	if(UserFunctions::getLoggedIn() != NULL && true == true){ // TODO: Add permission check
		// Grab the class to walk through the DB
		ImportClass("Config.ConfigOptions");
		
		// Display the page text
		echo Text::pageTitle("Manage Configuration Options");
		
		// TODO: Add view expired
		?>
		<div class="btn-toolbar pull-right" style="margin-top: 0px;">
			<div class="btn-group">
				<a class="btn" href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=configGenerator&task=add"><i class="icon-plus"></i></a>
				<a class="btn" href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=help&act=manage&com=ajaxHandler"><i class="icon-question-sign"></i></a>
			</div>
		</div>
		<?php
		
		// Create the class
		$items = new ConfigOptions();
		
		?>
		<form name="adminForm" method="post" action="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=configGenerator&task=manage">
			<?php
			if($items->rowsExist()){
			?>
			<table class="table table-bordered table-striped table-hover">
				<thead>
					<tr>
						<th width="5%">
							
						</th>
						<th width="30%">
							Component
						</th>
						<th width="25%">
							Type
						</th>
						<th width="35%">
							Name
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
										<li><a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=configGenerator&task=edit&id=<?php echo $row->getId(); ?>"><i class="icon-pencil"></i> Edit</a></li>
										<li><a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=configGenerator&task=delete&id=<?php echo $row->getId(); ?>"><i class="icon-trash"></i> Delete</a></li>
									</ul>
								</li>
							</ul>
						</td>
						<td>
							<?php echo $row->getComponent(1); ?>
						</td>
						<td>
							<?php echo $row->getType(1); ?>
						</td>
						<td>
							<?php echo $row->getName(1); ?>
						</td>
						<td>
							<a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=development&act=configGenerator&task=details&id=<?php echo $row->getId(); ?>">Details</a>
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
		Url::redirect(UserFunctions::getLoginUrl(), 0, false);
	}
}

?>