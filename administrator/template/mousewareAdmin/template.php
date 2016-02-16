<?php $contentVarName; ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><mse:include type="header" name="title" /></title>
<mse:include type="header" />
<link href="<?php echo Url::getAdminHttpBase('template'); ?>/mousewareAdmin/bootstrap/css/bootstrap.css" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo Url::getAdminHttpBase('template'); ?>/mousewareAdmin/font-awesome/css/font-awesome.min.css" />
<link rel="stylesheet" href="<?php echo Url::getAdminHttpBase('template'); ?>/mousewareAdmin/css/main.css" />

<script type="text/javascript" src="<?php echo Url::getAdminHttpBase('template'); ?>/mousewareAdmin/jquery/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="<?php echo Url::getAdminHttpBase('template'); ?>/mousewareAdmin/jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo Url::getAdminHttpBase('template'); ?>/mousewareAdmin/bootstrap/js/bootstrap.min.js"></script>
<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script type="text/javascript">
$(document).ready(function() {
	$('[data-toggle=offcanvas]').click(function(){
		$('.row-offcanvas').toggleClass('active');
	});
});
</script>
<style type="text/css">
body {
	background: url("<?php echo Url::getAdminHttpBase('template'); ?>/mousewareAdmin/images/background.png") repeat #EDEDED;
}

.buttonMenu {
	margin-bottom: 10px;
}

#footer {
	font-size: 9pt;
	color: #999999;
	padding: 10px;
}

#topMenu .navbar {
	border-top: 4px solid #8E2322;
	margin-bottom: 0px;
}

.headerDiv {
	border-bottom: 1px solid rgba(0, 0, 0, 0.3);
	box-shadow: 0px 1px 0px rgba(255, 255, 255, 0.05) inset;
	text-shadow: 0px 1px 0px rgba(0, 0, 0, 0.5);
	background-color: #444444;
}

.headerDiv {
	padding: 10px;
}

.headerDiv h3 {
	margin: 0px;
	color: #BABABA;
}

#content {
	padding: 10px;
	background-color: #6e6e6e;
}

#content .contentArea {
	padding-left: 10px;
	padding-right: 10px;
	padding-bottom: 10px;
	background-color: #FFFFFF;
	border: 10px solid #dddddd;
}

.quick-btn {
  background: #EEEEEE;
  -webkit-box-shadow: 0 0 0 1px #F8F8F8 inset, 0 0 0 1px #CCCCCC;
  box-shadow: 0 0 0 1px #F8F8F8 inset, 0 0 0 1px #CCCCCC;
  color: #444444;
  display: inline-block;
  height: 80px;
  margin: 10px;
  padding-top: 16px;
  text-align: center;
  text-decoration: none;
  text-shadow: 0 1px 0 rgba(255, 255, 255, 0.6);
  width: 90px;
  position: relative;
}

.quick-btn span {
  display: block;
}

.quick-btn .label {
  position: absolute;
  right: -5px;
  top: -5px;
}

.quick-btn:hover {
  text-decoration: none;
  color: #fff;
  background-color: #49AAAA;
  text-shadow: 0 1px 1px #000;
}

.quick-btn.small {
  width: 40px;
  height: 30px;
  padding-top: 6px;
}


.sidebar-nav {
	padding: 9px 0;
}

@media screen and (max-width: 768px){
	.row-offcanvas {
		position: relative;
		-webkit-transition: all 0.25s ease-out;
		-moz-transition: all 0.25s ease-out;
		transition: all 0.25s ease-out;
	}
	
	.row-offcanvas-right
	.sidebar-offcanvas {
		right: -50%;
	}
	
	.row-offcanvas-left
	.sidebar-offcanvas {
		left: -50%;
	}
	
	.row-offcanvas-right.active {
		right: 50%;
	}
	
	.row-offcanvas-left.active {
		left: 50%;
	}
	
	.sidebar-offcanvas {
		position: absolute;
		top: 0;
		width: 50%;
	}
}

/* Twitter Bootstrap Modifications*/
ul.nav li.dropdown:hover > ul.dropdown-menu {
    display: block;   
	margin: 0; 
}

</style>
</head>
<body>
	<div id="topMenu">
		<div class="navbar navbar-static-top navbar-inverse" role="navigation">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?php echo Url::home(); ?>">Admin Panel</a>
				<mse:include type="component" name="menu" position="top" />
			</div>
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav navbar-right">
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
	<mse:include type="component" name="contentBar" />
	<div id="content">
		<div class="contentArea">
			<mse:include type="component" name="messages" />
			<mse:include type="component" />
		</div>
	</div>
	<div id="footer">
		<p>&copy; Mike Di Domenico <?php echo date("Y", time()); ?></p>
	</div>
</body>
</html>
