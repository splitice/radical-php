<?php
namespace Web\Pages\Special;
use Web\PageHandler;

class NotModified extends PageHandler\PageBase {
	function GET() {
		$headers = \Web\PageHandler::$stack->top()->headers;
		$headers->Status(304);

		return false;
	}
	function POST() {
		return $this->GET ();
	}
}