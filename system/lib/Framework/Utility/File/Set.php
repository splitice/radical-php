<?php
namespace Utility\File;
use Basic\Arr\Object\ArrayObject;

class Set extends ArrayObject {
	function Add(Instance $v){
		parent::Add($v);
	}
	function totalSize(){
		$sum = 0;
		foreach($this->data as $f){
			if($f instanceof Instance){
				$sum += $f->Size($f);
			}else{
				throw new \Exception('Invalid File Set');
			}
		}
		return $sum;
	}
}