<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

function Manage(){
	if(UserFunctions::hasComponentAccess("shortlink", "manage") == true){
		// Grab the class to walk through the DB
		ImportClass("ShortLink.ShortLinks");
		
		// Create the class
		$items = new ShortLinks();
		
		echo Text::pageTitle("Manage Short Links"); 
		
		?>
		<div class="buttonMenu toolbar pull-right">
			<div class="btn-group">
				<a class="btn btn-sm btn-default" href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=shortlink&act=add"><i class="fa fa-plus"></i></a>
				<a class="btn btn-sm btn-default" href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=help&com=shortlink"><i class="fa fa-question-circle"></i></a>
			</div>
		</div>
		<form name="adminForm" method="post" action="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=shortlink&act=manage">
			<?php
			if($items->rowsExist()){
			?>
				<table class="table table-bordered table-striped table-hover table-condensed table-responsive">
					<thead>
						<tr>
							<th width="5%">
								
							</th>
							<th width="15%">
								ID
							</th>
							<th width="30%">
								Redirect URL
							</th>
							<th width="10%">
								Internal
							</th>
							<th width="10%">
								Active
							</th>
							<th width="20%">
								Expiration
							</th>
							<th width="10%">
								Hits
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
							<ul class="nav">
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" style="margin-top: 0px; margin-bottom: 0px;"><?php echo $cnt; ?></a>
									<ul class="dropdown-menu">
										<li><a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=shortlink&act=edit&id=<?php echo $row->id; ?>"><i class="glyphicon glyphicon-pencil"></i> Edit</a></li>
										<li><a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=shortlink&act=delete&id=<?php echo $row->id; ?>"><i class="glyphicon glyphicon-trash"></i> Delete</a></li>
									</ul>
								</li>
							</ul>
						</td>
						<td>
							<a href="<?php echo Url::getHttpBase() . "/" . $row->id; ?>" target="_blank"><?php echo $row->id; ?></a>
						</td>
						<td>
							<?php echo $row->redirectLink; ?>
						</td>
						<td>
							<?php echo Text::getYesNo($row->internal); ?>
						</td>
						<td>
							<?php echo Text::getYesNo($row->active); ?>
						</td>
						<td>
							<?php echo ($row->expirationDate !== "0000-00-00 00:00:00" ? $row->expirationDate : "None"); ?>
						</td>
						<td>
							<?php echo $row->hitCount; ?>
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