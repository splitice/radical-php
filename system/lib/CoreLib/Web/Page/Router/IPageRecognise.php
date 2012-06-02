<?php
namespace Web\Page\Router;

interface IPageRecognise {
	static function Recognise(\Net\URL $url);
}