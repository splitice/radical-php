<?php
namespace Utility\Image\Filters\Internal;

abstract class FilterBase {
	function execute($gd){
		$data = $this->toData();
		return static::Filter($gd, $data);
	}
}