<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<?php 
echo '<script src="',\HTML\Javascript\Library::Find('jquery'),'" type="text/javascript"></script>';
echo \Web\Resource\Javascript::HTML('webgrind');
echo \Web\Resource\CSS::HTML('webgrind');
?>