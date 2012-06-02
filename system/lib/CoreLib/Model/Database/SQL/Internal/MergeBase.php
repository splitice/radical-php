<?php
namespace Database\SQL\Internal;

use Database\SQL\IStatement;
use Database\SQL\IMergeStatement;

abstract class MergeBase implements IMergeStatement {
	function mergeTo(IStatement $mergeIn){
		return $mergeIn->_mergeSet(get_object_vars($this));
	}
	function _mergeSet(array $in){
		$a = get_object_vars($this);
		foreach($a as $k=>$v){
			if(isset($in[$k])){
				if($in[$k]){
					if($this->$k instanceof IMergeStatement){
						$this->$k->_mergeSet(get_object_vars($in[$k]));
					}else{
						$this->$k = $in[$k];
					}
				}
			}
		}
		//$this->sql = null;
		return $this;
	}
}