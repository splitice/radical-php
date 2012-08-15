<?php
namespace Model\Database\ORM;

use Model\Database\Model\TableReferenceInstance;
use Model\Database\SQL\Parse\CreateTable\ColumnReference;
use Model\Database\SQL\Parse\CreateTable;

class MyIsam {
	static function fieldReferences(CreateTable $structure,TableReferenceInstance $table){
		$ret = array();
		foreach($structure as $field=>$statement){
			$ref = ModelReference::Find($field);
			if($ref != $table){
				$ret[$field] = new ColumnReference($ref->getTable(), $field);
			}
		}
		return $ret;
	}
}