<?php
namespace Web\Page\Router;

interface IPageRecognise {
	static function recognise(\Utility\Net\URL $url);
}