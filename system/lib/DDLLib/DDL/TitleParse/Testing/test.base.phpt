<?php
function __autoload($className){
	include(__DIR__.'/../../../'.str_replace('\\','/',$className).'.php');
}