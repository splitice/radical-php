<?php
namespace Web\Resource;

class Javascript extends ResourceBase {
	const PATH = 'js';
	
	protected static function _HTML($path){
		return new \HTML\Tag\Script($path);
	}
}