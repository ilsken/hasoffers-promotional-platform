<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo $this->getTitle()?> | <?=Conf::read('ENV.CompanyName');?></title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="shortcut icon" href="http://www.go2media.org/assets/img/favicon/favicon.ico" type="image/x-icon">
    <link rel="icon" href="http://www.go2media.org/assets/img/favicon/favicon.ico" type="image/ico" /> 
    <script src="/js/jquery-1.3.2.min.js" type="text/javascript"></script>
    <script src="/js/cufon.js" type="text/javascript"></script>
    <script src="/js/National_Bold_800.font.js" type="text/javascript"></script>
    <script src="/js/National_Medium_600.font.js" type="text/javascript"></script>
    <script src="/js/main.js" type="text/javascript"></script>
    
    <!-- Page Specific Includes -->
    <?= $this->getRequiredResourceHeader() ?>
</head>
<body>
    <div id="wrap">
	<div id="header">  
	    <div id="top">  
		<div id="logo">
			<img src="/images/p-logo.png"/>
		</div>
		<div id="auth">
		    <?php if ( Session::read( 'hasAuth' ) ) { ?>
			<a href="/contact">Contact</a>&nbsp;|&nbsp;<a href="/logout">Logout</a>
		    <?php } else { ?>
			<a href="/contact">Contact</a>
		    <?php } ?>
		</div>
	    </div>
	    <div id="nav">
		<ul>
		<?php if ( Session::read( 'hasAuth' ) ) { ?>
			<li><a href="/index"><span>Home</span></a></li><li><a href="/banners"><span>Banners</span></a></li><li><a href="/email"><span>Email</span></a></li><li><a href="/facebook"><span>Facebook</span></a></li><li><a href="/linkedin"><span>LinkedIn</span></a></li><li><a href="/twitter"><span>Twitter</span></a></li><li><a href="/statistics"><span>Earnings Report</span></a></li><li><a href="/refer"><span>Refer a Friend</span></a></li>
		<?php } else { ?>
			<li><a href="/signup"><a href="/login"><span>Login</span></a></li><li><a href="/signup"><span>Signup</span></a></li>      
		<?php } ?>
		</ul>
	    </div>
	</div>
	<div class="clear"></div>
	<div id="main">
	    <div id="content">
		<?php echo $this->showContent()?>
	    </div>
	</div>
	<div class="clear"></div>
	<div id="footer">    
		<div class="copyright">&copy;2010 <?=Conf::read('ENV.CompanyName');?>. All rights reserved.</div>
		<div class="footer-logo"></div>
	</div>
    </div>
</body>
</html>