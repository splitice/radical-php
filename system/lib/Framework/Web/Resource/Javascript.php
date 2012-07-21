<?php
namespace Web\Resource;

use Utility\HTML\Tag;

class Javascript extends ResourceBase {
	const PATH = 'js';
	
	protected static function _HTML($path){
		return new Tag\Script($path);
	}
}