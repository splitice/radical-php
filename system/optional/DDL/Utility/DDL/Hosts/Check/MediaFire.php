<?php
namespace Utility\DDL\Hosts\Check;

use Utility\File;

class MediaFire extends Internal\HostBase {
	const HOST_SCORE = 1.2;
	const HOST_ABBR = 'MF';
	const HOST_DOMAIN = 'mediafire.com';
	const IMAGE_ORDER = 2;
	
	function validateCheck($data) {
		$ret = new Internal\CheckReturn('dead');
		
		if(preg_match('#<input type="hidden" id="sharedtabsfileinfo1-fn" value="([^"]+)">#',$data,$m)){
			$ret->setStatus('ok');
			$ret->setFilename(trim($m[1]));
			if(preg_match('#<input type="hidden" id="sharedtabsfileinfo1-fs" value="([^"]+)">#',$data,$m)){
				$fs = File\Size::fromHuman($m[1]);
				$ret->setFilesize($fs);
			}
		}

		
		return $ret;
	}
}