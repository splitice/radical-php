<?php
namespace Web\Page\Controller\Special;

use Web\Page\Handler;

class NotModified extends Handler\PageBase {
	/**
	 * Handle GET request
	 *
	 * @throws \Exception
	 */
	function GET() {
		$headers = \Web\Page\Handler::$stack->top()->headers;
		$headers->Status(304);

		return false;
	}
	
	/**
	 * Handle POST request
	 *
	 * @throws \Exception
	 */
	function pOST() {
		return $this->GET ();
	}
}