<?php
namespace Web\Page\Controller;

use Web\Page\Handler\PageBase;
use Web\Page\Handler;

class TransparentGif extends PageBase {		
	/**
	 * Handle GET request
	 *
	 * @throws \Exception
	 */
	function GET(){
		\Web\Page\Handler::top()->headers['Content-Type'] = 'image/gif';
		echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
	}
}