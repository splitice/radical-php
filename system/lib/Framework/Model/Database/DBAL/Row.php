<?php
namespace Model\Database\DBAL;
use Basic\Arr\Object\CollectionObject;

class Row extends CollectionObject {
	function __get($k){
		return $this->Get($k);
	}
}