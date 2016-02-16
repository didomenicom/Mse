<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><mse:include type="header" name="title" /></title>
<mse:include type="header" />
<script type="text/javascript" src="<?php echo Url::getHttpBase('template'); ?>/mouseware/jquery/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="<?php echo Url::getHttpBase('template'); ?>/mouseware/bootstrap/js/bootstrap.min.js"></script>
<link href="<?php echo Url::getHttpBase('template'); ?>/mouseware/bootstrap/css/bootstrap.css" rel="stylesheet" />
<link href="<?php echo Url::getHttpBase('template'); ?>/mouseware/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<style type="text/css">
body {
	padding-top: 60px;
	padding-bottom: 40px;
}
.sidebar-nav {
	padding: 9px 0;
}
ul.nav li.dropdown:hover > ul.dropdown-menu{
    display: block;   
	margin: 0; 
}
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
		<div class="span12">
			<div class="well">
				<mse:include type="component" name="messages" />
				<mse:include type="component" />
			</div>
		</div>
	</div>
</div>
<div align="center" style="font-size:8pt; color:#999999;">&copy; <?php echo date("Y", time()); ?> Mouseware Designs</div>
</body>
</html>