<?php
namespace Image\Graph\Schema\Internal;

abstract class SchemaBase implements \JsonSerializable {
	function jsonSerialize(){
		$t = clone $this;
		foreach($t as $k=>$v){
			if($v === null){
				unset($t->$k);
			}
		}
		return $t;
	}
}