<link rel="shortcut icon" href="<?=$_->u('favicon.ico');?>" type="image/x-icon"/>
<meta name="apple-mobile-web-app-capable" content="yes"/>
<meta name="apple-mobile-web-app-status-bar-style" content="black" />

<?php 
if($_->handler){
	echo '<title>',$_->handler->Title(),'</title>';
}

if($_->handler && $_->handler instanceof \Web\Page\Handler\IMeta){
	echo $_->handler->meta('charset');
	echo $_->handler->meta('robots');
	echo $_->handler->meta('description');
	echo $_->handler->meta('keywords');
	echo $_->handler->meta('canonical');
}

if(\Web\Resource\CSS::Exists('main')){
	echo \Web\Resource\CSS::HTML('main');
}elseif(\Web\Resource\CSS::Exists('print')){
	echo \Web\Resource\CSS::HTML('print','print');
}

Web\Mobile\HTML::Output();

if(\Web\Resource\Javascript::Exists('main')){
	echo \Web\Resource\Javascript::HTML('main');
}
?>