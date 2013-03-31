<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><mse:include type="header" name="title" /></title>
<mse:include type="header" />
<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
<script type='text/javascript' src="<?php echo Url::getAdminHttpBase('template'); ?>/mousewareAdmin/bootstrap/js/bootstrap.min.js"></script>
<script type='text/javascript' src="<?php echo Url::getAdminHttpBase('template'); ?>/mousewareAdmin/js/bootbox.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo Url::getAdminHttpBase('template'); ?>/mousewareAdmin/bootstrap/css/bootstrap-responsive.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Url::getAdminHttpBase('template'); ?>/mousewareAdmin/bootstrap/css/bootstrap.css" />

<style type="text/css">
body {
	padding-top: 60px;
	padding-bottom: 40px;
}
.sidebar-nav {
	padding: 9px 0;
}

/* Twitter Bootstrap Modifications*/
ul.nav li.dropdown:hover > ul.dropdown-menu {
    display: block;   
	margin: 0; 
}

.controls .input-error{border-style:solid;border-color:#b94a48;}
</style>
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container-fluid">
			<a class="brand" href="<?php echo Url::home(); ?>">Mouseware</a>
			<mse:include type="component" name="menu" position="top" />
			<ul class="nav pull-right">
				<li class="dropdown" id="accountmenu">
					<?php
					if(UserFunctions::getLoggedIn() != NULL){
						?>
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Logged in as <?php echo UserFunctions::getLoggedIn()->getUsername(); ?></a>
						<ul class="dropdown-menu">
							<li><a href="<?php echo Url::getHttpBase(); ?>">Front-end</a></li>
							<mse:include type="component" name="menu" position="account" />
							<li class="divider"></li>
							<li><a href="<?php echo UserFunctions::getLogoutUrl(); ?>">Logout</a></li>
						</ul>
						<?php
					} else {
						?>
						<a href="<?php echo UserFunctions::getLoginUrl(); ?>" class="navbar-link">Login</a>
						<?php
					}
					?>
				</li>
			</ul>
		</div>
	</div>
</div>
<div class="container-fluid">
	<div class="row-fluid">
		<?php
		if(UserFunctions::getLoggedIn() != NULL && MenuGenerator::itemsExist("left") == true){
		?>
		<div class="span2">
			<div class="well sidebar-nav">
				<ul class="nav nav-list">
					<li class="nav-header">Sidebar</li>
					<mse:include type="component" name="menu" position="left" />
				</ul>
			</div>
		</div>
		<div class="span10">
			<div class="well">
				<mse:include type="component" name="messages" />
				<mse:include type="component" />
			</div>
		</div>
		<?php
		} else {
		?>
		<div class="span12">
			<div class="well">
				<mse:include type="component" name="messages" />
				<mse:include type="component" />
			</div>
		</div>
		<?php
		}
		?>
	</div>
</div>
<div align="center" style="font-size:8pt; color:#999999;">&copy; <?php echo date("Y", time()); ?> Mouseware Designs</div>
</body>
</html>