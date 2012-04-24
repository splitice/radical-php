<meta charset="utf-8">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
<meta name="apple-mobile-web-app-capable" content="yes"/>
<meta name="apple-mobile-web-app-status-bar-style" content="black" />

<?php 
Web\Mobile\HTML::Output();
?>

<title><?=$_->handler->Title();?></title>

<?php 
/*
 *  manifest="/cache.manifest"
 * if(\Server::isProduction()){
	echo '<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.css" />';
	echo '<script src="http://code.jquery.com/jquery-1.6.4.min.js"></script>';
	echo '<script src="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.js"></script>';
}else{*/
//	echo '<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.css" />';
//	echo '<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>';
//	echo '<script src="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.js"></script>';
//}
?>

<?php 
if(\Web\Resource\CSS::Exists('main')){
	echo \Web\Resource\CSS::HTML('main');
}elseif(\Web\Resource\CSS::Exists('print')){
	echo \Web\Resource\CSS::HTML('print','print');
}
?>