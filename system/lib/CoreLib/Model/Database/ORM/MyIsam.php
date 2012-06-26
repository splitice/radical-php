<?php
namespace Model\Database\ORM;

use Model\Database\SQL\Parse\CreateTable\ColumnReference;

class MyIsam {
	static function fieldReferences(CreateTable $structure){
		$ret = array();
		foreach($structure as $field=>$statement){
			$ref = ModelReference::Find($field);
			if($ref != $this->table){
				$ret[$field] = new ColumnReference($ref->getTable(), $field);
			}
		}
		return $ret;
	}
}