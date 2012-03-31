<?php
namespace Web\PageRecogniser;

interface IPageRecognise {
	static function Recognise(\Net\URL $url);
}