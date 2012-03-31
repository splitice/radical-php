<?php
namespace Image\Filters\Internal;

abstract class FilterBase {
	function Execute($gd){
		$data = $this->toData();
		return static::Filter($gd, $data);
	}
}