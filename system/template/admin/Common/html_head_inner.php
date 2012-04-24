<meta charset="utf-8">

<title><?=$_->handler->Title();?></title>

<?php 
if(\Web\Resource\Javascript::Exists('admin')){
	echo \Web\Resource\Javascript::HTML('admin');
}
if(\Web\Resource\CSS::Exists('admin')){
	echo \Web\Resource\CSS::HTML('admin');
}elseif(\Web\Resource\CSS::Exists('print')){
	echo \Web\Resource\CSS::HTML('print','print');
}
?>