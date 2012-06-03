<?php
namespace Web\Resource;

use Utility\HTML\Tag;

class CSS extends ResourceBase {
	const PATH = 'css';
	
	static function HTML($name,$media = null){
		$ret = parent::HTML($name);
		if($media !== null) $ret->attribute['media'] = $media;
		return $ret;
	}
	protected static function _HTML($path){
		return new Tag\Link($path);
	}
}