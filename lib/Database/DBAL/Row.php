<?php
namespace Database\DBAL;
use \Basic\ArrayLib\Object\CollectionObject;

class Row extends CollectionObject {
	function __get($k){
		return $this->Get($k);
	}
}