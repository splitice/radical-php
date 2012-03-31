<?php
namespace Image\Graph\Schema;

use Basic\ArrayLib\Object\CollectionObject;

class Dataset extends CollectionObject implements \JsonSerializable {
	function jsonSerialize(){
		$t = $this->asArray();
		if(isset($t['X'])) unset($t['X']);
		return $t;
	}
}