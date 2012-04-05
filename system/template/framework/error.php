<!DOCTYPE html>
<html>
<head>
	<title><?=$_->vars['error']->getHeading()?></title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="robots" content="noarchive" />

	<style type="text/css">
	body {
		background: url(http://cdn1.editmysite.com/images/404background.jpg);
		font-family: Helvetica, arial, sans-serif;
		color: #ccc;
	}
	.alert-container {
		background: none repeat scroll 0 0 #1B1B1B;
	    border: 1px solid #4C4C4C;
	    border-radius: 4pt;
	    padding: 20px;
	    width: 918px;
		margin: 20px auto 0;
	}
	.alert-inner {
		padding-left: 100px;
	}
	.alert-bg {
		background: url(http://i.lulzimg.com/82e4954fab.png) no-repeat;
	}
	.alert-heading {
		font-size: 40px;
		font-weight: bold;
		line-height: 40px;
	}
	.alert-subheading {
		margin-top: 8px;
		font-size: 25px;
		line-height: 25px;
	}
	.redirect {
		width: 918px;
		margin: 24px auto 0px;
		font-size: 14px;
		line-height: 14px;
		text-align: center;
	}
	.redirect a {
		color: #ffb300;
		text-decoration: none;
	}
	</style>
</head>
<body>
	<div class="alert-container">
		<div class="alert-bg">
			<div class="alert-inner">
				<div class="alert-heading"><?=$_->vars['error']->getHeading()?></div>
				<div class="alert-subheading"><?=$_->vars['error']->getMessage()?></div>
			</div>
		</div>
	</div>
	<div class="alert-container">
		<div class="alert-inner">
			<div class="alert-heading">Backtrace</div>
			<div class="alert-subheading"><?=nl2br($_->vars['error']->getTraceOutput(),true)?></div>
		</div>
	</div>
	<div class="redirect">We are sorry for any inconvenience this has caused.</div>
</body>
</html>