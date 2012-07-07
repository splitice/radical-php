<meta charset="utf-8">

<title><?=$_->handler->Title();?></title>

<?php 
if(\Web\Resource\Javascript::Exists('main')){
	echo \Web\Resource\Javascript::HTML('main');
}
if(\Web\Resource\CSS::Exists('admin')){
	echo \Web\Resource\CSS::HTML('admin');
}elseif(\Web\Resource\CSS::Exists('print')){
	echo \Web\Resource\CSS::HTML('print','print');
}
?>