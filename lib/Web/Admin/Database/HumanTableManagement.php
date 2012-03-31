<?php
namespace Web\Admin\Database;

use Database\Model\Table\TableManagement;

class HumanTableManagement extends TableManagement {
	function RenameColumn($k){
		$orm = $this->table->getORM();
		if(count($orm->id) == 1){
			if($orm->id[0] == $k){
				return 'ID';
			}
		}
	
		if(isset(static::$map[$k])){
			return static::$map[$k];
		}
	
		$v = $orm->getMappings()->stripPrefix($k);
		if($v) return $v;
	
		return $k;
	}
	
	function getColumns(){
		$r = parent::getColumns();
		$ret = array();
		foreach($r as $k=>$v){
			$ret[$this->RenameColumn($k)] = $v;
		}
		return $ret;
	}
	
	function __construct($table){
		if($table instanceof TableManagement){
			$table = $table->getTable();
		}
		parent::__construct($table);
	}
}