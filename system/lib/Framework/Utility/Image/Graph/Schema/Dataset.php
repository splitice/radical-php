<?php
namespace Utility\Image\Graph\Schema;

use Basic\Arr\Object\CollectionObject;

class Dataset extends CollectionObject implements \JsonSerializable {
	function jsonSerialize(){
		$t = $this->toArray();
		if(isset($t['X'])) unset($t['X']);
		return $t;
	}
}