<?php
namespace Web\Page\Controller\Special;

use Web\Page\Handler;

class NotModified extends Handler\PageBase {
	function GET() {
		$headers = \Web\Page\Handler::$stack->top()->headers;
		$headers->Status(304);

		return false;
	}
	function POST() {
		return $this->GET ();
	}
}