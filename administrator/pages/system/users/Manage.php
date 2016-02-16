<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

function Manage(){
	if(UserFunctions::hasComponentAccess("users", "manage") == true){
		// Grab the class to walk through the DB
		ImportClass("User.Users");
		ImportClass("Ajax.Ajax");
		
		// Display the page text
		echo Text::pageTitle("Manage Users");
		
		// Figure out the actions
		$viewDeleted = (Url::getParts('deleted') == 1 ? true : false);
		
		?>
		<div class="btn-toolbar pull-right" style="margin-top: 0px;">
			<div class="btn-group">
				<a class="btn" href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=users&act=add"><i class="icon-plus"></i></a>
				<a class="btn" href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=config&act=edit&com=users"><i class="icon-wrench"></i></a>
				<a class="btn" href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=help&com=users"><i class="icon-question-sign"></i></a>
				<a class="btn dropdown-toggle dropdown" data-toggle="dropdown" href="#" style="padding-left:14px; padding-right:14px;"><i class="icon-eye-open"></i></a>
				<ul class="dropdown-menu">
					<li><a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=users&act=manage&deleted=<?php echo ($viewDeleted == true ? 0 : 1); ?>">View <?php echo ($viewDeleted == true ? "Non-Deleted" : "Deleted"); ?></a></li>
				</ul>
			</div>
		</div>
		<?php
		// Setup the filters
		$filter['hasAccess'] = true;
		$filter['deleted'] = $viewDeleted;
		$filter['permissionGroup'] = (Session::get('filter_userPermissionGroup') != NULL ? Session::get('filter_userPermissionGroup') : NULL);
		
		$sort['by'] = "name";
		$sort['direction'] = "ASC";
		
		// Create the class
		$items = new Users($filter, $sort);
		
		// TODO: Add to header (render class function)
		?>
		<script type="text/javascript">
		$(document).ready(function(){ 
			$("#filter_userPermissionGroup").change(function(e){
				var selectVal = $(this).find("option:selected").val();
				
				$.ajax({
					type: "POST",
					url: "<?php echo Url::getAdminHttpBase(); ?>/index.php?ajaxRequest=1",
					async: true,
					timeout: 50000,
					data: {id: "<?php echo Ajax::generateHandlerId("Manage Users Permission Group Dropdown"); ?>", 
						task: "updateUserList", 
						val: selectVal, 
						deleted: <?php echo ($viewDeleted == true ? 1 : 0); ?>},
					success: function(data){
						updateRows(data);
					}
				});
			});
			
			$("#filter_userSearch").live("keyup", function(e){
				var searchStr = $(this).val();

				if(searchStr !== ""){
					$.ajax({
						type: "POST",
						url: "<?php echo Url::getAdminHttpBase(); ?>/index.php?ajaxRequest=1",
						async: true,
						timeout: 50000,
						data: {id: "<?php echo Ajax::generateHandlerId("Manage Users Permission Group Dropdown"); ?>", 
							task: "updateSearch", 
							val: searchStr, 
							deleted: <?php echo ($viewDeleted == true ? 1 : 0); ?>},
						success: function(data){
							updateRows(data);
						}
					});
				} else {
					$.ajax({
						type: "POST",
						url: "<?php echo Url::getAdminHttpBase(); ?>/index.php?ajaxRequest=1",
						async: true,
						timeout: 50000,
						data: {id: "<?php echo Ajax::generateHandlerId("Manage Users Permission Group Dropdown"); ?>", 
							task: "updateUserList", 
							val: 0, 
							deleted: <?php echo ($viewDeleted == true ? 1 : 0); ?>},
						success: function(data){
							updateRows(data);
						}
					});
				}
			});

			$(".sort").click(function(e){
				var str = $(this).attr("id");
				
				if(str !== ""){
					$.ajax({
						type: "POST",
						url: "<?php echo Url::getAdminHttpBase(); ?>/index.php?ajaxRequest=1",
						async: true,
						timeout: 50000,
						data: {id: "<?php echo Ajax::generateHandlerId("Manage Users Permission Group Dropdown"); ?>", 
							task: "sort", 
							val: str},
						success: function(data){
							if(data == 1){
								// Save off search query 
								var userSearch = $("#filter_userSearch").val();
								var userPermissionGroup = $("#filter_userPermissionGroup").find("option:selected").val();
								window.location.replace("<?php echo Url::getAdminHttpBase(); ?>/index.php?option=users&act=manage" + ((userSearch != 0 || userSearch != "") ? "&q=" + userSearch : "") + (userPermissionGroup != 0 ? "&f=" + userPermissionGroup : ""));
							} else {
								// TODO: Add error message 
							}
						}
					});
				}
			});

			function updateRows(data){
				if(data == 0){
					$("#manageTable tbody").html("");
				}

				if(data != "" && data != 0){
					$("#manageTable tbody").html(data);
				}

				// Handle the footer 
				var totalRowCount = (data != "" && data != 0 ? $('#manageTable tbody tr').length : 0);

				$("#tableFooterRowCount").html((totalRowCount > 0 ? "1" : "0") + " - " + totalRowCount + " of " + totalRowCount + " records");
			}
		});
		</script>
		<form name="adminForm" method="post" action="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=users&act=manage">
			<?php
			if($items->rowsExist()){
			?>
			<table id="manageTable" class="table table-bordered table-striped table-hover">
				<thead>
					<tr>
						<th colspan="7">
							<div class="input-prepend pull-left dropdown">
								<span class="add-on"><i class="icon-search"></i> Search </span>
								<input type="text" name="filter_userSearch" id="filter_userSearch" autocomplete="off" value="<?php echo (isset($_GET["q"]) ? $_GET["q"] : ""); ?>">
							</div>
							<div class="pull-right">
								<select name="filter_userPermissionGroup" id="filter_userPermissionGroup">
									<option value="0"<?php echo (Session::get('filter_userPermissionGroup') == NULL ? "selected=\"selected\"" : "")?>>- Permission Group -</option>
									<?php
									ImportClass("Group.Groups");
									$filter['hasAccess'] = true;
									$permissionGroups = new Groups(array("hasAccess" => true));
									
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
							Name
						</th>
						<th width="18%">
							Username
						</th>
						<th width="18%">
							Email Address
						</th>
						<th width="18%">
							Permission Group
						</th>
						<th width="18%">
							Last Login
						</th>
						<th width="5%">
							ID
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
										<li><a href="<?php echo Url::getAdminHttpBase(); ?>/index.php?option=users&act=details&id=<?php echo $row->getId(); ?>"><i class="icon-list-alt"></i> Details</a></li>
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
		Url::redirect(Url::home(), 3, false);
	}
}

?>
