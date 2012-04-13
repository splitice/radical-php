<?php
namespace Web\Resource;

class CSS extends ResourceBase {
	const PATH = 'css';
	
	static function HTML($name,$media){
		$ret = parent::HTML($name);
		$ret->attribute['media'] = $media;
		return $ret;
	}
	protected static function _HTML($path){
		return new \HTML\Tag\Link($path);
	}
}