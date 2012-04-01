<?php
namespace Database\SQL\Parse;

class DataType {
	static function fromSQL($type,$size){
		$type = strtolower($type);
		foreach(\ClassLoader::getNSExpression('Database\\SQL\\Parse\\Types\\*') as $t){
			if($t::is($type)){
				return new $t($type,$size);
			}
		}
	}
}