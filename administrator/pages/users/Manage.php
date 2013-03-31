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
		ImportClass("User.Users");
		
		// Display the page text
		echo Text::pageTitle("Manage Users");
		
		// Figure out the actions
		$viewDeleted = (Url::getParts('deleted') == 1 ? true : false);
		
		?>
		<div class="btn-toolbar pull-right" style="margin-top: 0px;">
			<div class="btn-group">
				<a class="btn" href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=users&act=add"><i class="icon-plus"></i></a>
				<a class="btn" href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=config&act=manage&com=users"><i class="icon-wrench"></i></a>
				<a class="btn" href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=help&act=manage&com=users"><i class="icon-question-sign"></i></a>
				<a class="btn dropdown-toggle dropdown" data-toggle="dropdown" href="#" style="padding-left:14px; padding-right:14px;"><i class="icon-eye-open"></i></a>
				<ul class="dropdown-menu">
					<li><a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=users&act=manage&deleted=<?php echo ($viewDeleted == true ? 0 : 1); ?>">View <?php echo ($viewDeleted == true ? "Non-Deleted" : "Deleted"); ?></a></li>
				</ul>
			</div>
		</div>
		<?php
		
		// Create the class
		$filter['deleted'] = $viewDeleted;
		$filter['permissionGroup'] = (Session::get('filter_userPermissionGroup') != NULL ? Session::get('filter_userPermissionGroup') : NULL);
		$items = new Users($filter);
		
		// TODO: Add to header (render class function)
		?>
		<script type="text/javascript">
		$(document).ready(function(){ 
			$("#filter_userPermissionGroup").change(function(){
				var optionSelect = this;
				$.ajax({
					type: "POST",
					url: "<?php echo Url::getAdminHttpBase(); ?>/index.php?ajaxRequest=1",
					async: true,
					timeout: 50000,
					data: {id: "wGmCO63M4HQZbeNmeW1mO1IKvpxq0QN8vRsoNXV1+2k=", task: "updateUserList", val: $(optionSelect).find("option:selected").val()},
					success: function(data){
						if(data != ""){
							var rowArray = data.split("**|||**");

							if(rowArray.length > 0){
								// There are new rows, clear current rows 
								$("#manageTable tbody").html("");
								var totalRowCount = 0;
								
								for(var i = 0; i < rowArray.length; i++){
									// Loop through and handle each row 
									var rowVal = rowArray[i];
									var str = "";
									var rowTdArray = rowVal.split("||");

									if(rowTdArray.length == 6){
										for(var x = 0; x < rowTdArray.length; x++){
											str = str + "<td>" + rowTdArray[x] + "</td>";
										}
										
										var options = '<ul class="nav" style="margin-top: 0px; margin-bottom: 0px;">' + 
														'<li class="dropdown">' + 
															'<a href="#" class="dropdown-toggle" data-toggle="dropdown" style="margin-top: 0px; margin-bottom: 0px;">' + (i + 1) + '</a>' + 
															'<ul class="dropdown-menu">' + 
																'<li><a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=users&act=edit&id=' + rowTdArray[(rowTdArray.length - 1)] + '"><i class="icon-pencil"></i> Edit</a></li>' + 
																'<li><a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=users&act=delete&id=' + rowTdArray[(rowTdArray.length - 1)] + '"><i class="icon-trash"></i> Delete</a></li>' + 
															'</ul>' + 
														'</li>' + 
													'</ul>';
										
										$('#manageTable > tbody').append("<tr><td>" + options + "</td>" + str + "</tr>");
										totalRowCount++;
									}
								}

								// Handle the footer 
								$("#tableFooterRowCount").html((totalRowCount > 0 ? "1" : "0") + " - " + totalRowCount + " of " + totalRowCount + " records");
							}
						}
					}
				});
			});
		});
		</script>
		<div id="textTmp"></div>
		<div id="textDbg"></div>
		<form name="adminForm" method="post" action="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=users&act=manage">
			<?php
			if($items->rowsExist()){
			?>
			<table id="manageTable" class="table table-bordered table-striped table-hover">
				<thead>
					<tr>
						<th colspan="7">
							<div class="input-append pull-left">
								<input type="text" name="filter_userSearch" id="filter_userSearch">
								<button class="btn" type="button"><i class="icon-search"></i> Search</button>
							</div>
							<div class="pull-right">
								<select name="filter_userPermissionGroup" id="filter_userPermissionGroup">
									<option value="0"<?php echo (Session::get('filter_userPermissionGroup') == NULL ? "selected=\"selected\"" : "")?>>- Permission Group -</option>
									<?php
									ImportClass("Group.Groups");
									$permissionGroups = new Groups();
									
									if($permissionGroups->rowsExist()){
										while($permissionGroups->hasNext()){
											$row = $permissionGroups->getNext();
											?>
									<option value="<?php echo $row->getId(); ?>"<?php echo (Session::get('filter_userPermissionGroup') == $row->getId() ? "selected=\"selected\"" : "")?>><?php echo $row->getName(); ?></option>
											<?php
										}
									}
									?>
								</select>
							</div>
						</th>
					</tr>
					<tr>
						<th width="5%">
							
						</th>
						<th width="18%">
							<?php echo Text::sortHeader("Name", "name", true); ?>
						</th>
						<th width="18%">
							<?php echo Text::sortHeader("Username", "username", false); ?>
						</th>
						<th width="18%">
							<?php echo Text::sortHeader("Email Address", "email", false); ?>
						</th>
						<th width="18%">
							<?php echo Text::sortHeader("Permission Group", "permissionGroup", false); ?>
						</th>
						<th width="18%">
							<?php echo Text::sortHeader("Last Login", "lastLogin", false); ?>
						</th>
						<th width="5%">
							<?php echo Text::sortHeader("ID", "id", false); ?>
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
										<li><a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=users&act=edit&id=<?php echo $row->getId(); ?>"><i class="icon-pencil"></i> Edit</a></li>
										<li><a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=users&act=delete&id=<?php echo $row->getId(); ?>"><i class="icon-trash"></i> Delete</a></li>
									</ul>
								</li>
							</ul>
						</td>
						<td>
							<?php echo $row->getName(1); ?>
						</td>
						<td>
							<?php echo $row->getUsername(1); ?>
						</td>
						<td>
							<?php echo $row->getEmail(1); ?>
						</td>
						<td>
							<?php echo $row->getPermissionGroup(1); ?>
						</td>
						<td>
							<?php echo $row->getLastLogin(1); ?>
						</td>
						<td>
							<?php echo $row->getId(); ?>
						</td>
					</tr>
					<?php
					$cnt++;
				}
				?>
					</tbody>
					<tfoot>
						<tr>
							<td align="center" colspan="7">
								<div class="pull-left span5">
									<span id="tableFooterRowCount"><?php echo $items->getStartNumber() . " - " . (($items->getStartNumber() + $items->getRowCount()) - 1) . " of " . $items->getTotalRows() . " records"; ?></span> 
										<?php
										if($items->getRowCount() != $items->getTotalRows()){
										?>
										<select class="span2" style="margin-bottom:0px;">
											<option>1</option>
											<option>2</option>
											<option>3</option>
											<option>4</option>
											<option>5</option>
										</select>
										<span>Per Page</span>
										<?php
										}
										?>
								</div>
								<?php
								if($items->getRowCount() != $items->getTotalRows()){
								?>
								<div class="pagination pagination-right" style="margin:0px;">
									<ul>
										<li><a href="#">&laquo;</a></li>
										<li><a href="#">1</a></li>
										<li><a href="#">2</a></li>
										<li class="active"><a href="#">3</a></li>
										<li><a href="#">4</a></li>
										<li><a href="#">5</a></li>
										<li><a href="#">&raquo;</a></li>
									</ul>
								</div>
								<?php
								}
								?>
							</td>
						</tr>
					</tfoot>
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