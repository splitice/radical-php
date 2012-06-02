<?php
namespace Database\SQL\Parts\Internal;

abstract class AliasPartBase extends PartBase {
	protected $alias;
	protected $expr;
	
	function __construct($expr,$alias){
		$this->expr = $expr;
		$this->alias = $alias;
	}
	
	function toSQL(){
		$ret = $this->expr;
		if($this->alias !== null){
			$ret .= ' AS '.$this->alias;
		}
		return $ret;
	}
}