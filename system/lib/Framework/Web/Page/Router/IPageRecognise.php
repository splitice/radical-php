<?php
namespace Web\Page\Router;

interface IPageRecognise {
	static function Recognise(\Utility\Net\URL $url);
}