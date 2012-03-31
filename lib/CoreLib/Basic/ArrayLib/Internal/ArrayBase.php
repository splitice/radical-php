<?php
namespace Basic\ArrayLib\Internal;

abstract class ArrayBase extends \Core\Object {
	static function isArray($in){
		if(is_array($in)){
			return true;
		}
		if(is_object($in) && $in instanceof \ArrayAccess){
			return true;
		}
		return false;
	}
	static function toArray($in){
		if(is_object($in) && $in instanceof \Traversable){
			$ret = array();
			foreach($in as $k=>$v){
				$ret[$k] = $v;
			}
			return $ret;
		}
		return false;
	}
	static function parameterHandle(&$p){
		if(self::isArray($p)){
			return;
		}
		$r = self::toArray($p);
		if($r){
			$p = $r;
			return;
		}
		throw new \BadMethodCallException('Parameter must be an array');
	}
}