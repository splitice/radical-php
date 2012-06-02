<?php
namespace Database\SQL\Parts\Internal;

abstract class JoinPartBase extends PartBase {
	const JOIN_TYPE = '';
	
	protected $table;
	protected $on;
	
	function __construct($table,$on){
		$this->table = $table;
		$this->on = $on;
	}
	
	function toSQL(){
		$ret = static::JOIN_TYPE;
		if($ret) $ret .= ' ';
		$ret .= 'JOIN '.$this->table.' ON ('.$this->on.')';
		return $ret;
	}
}