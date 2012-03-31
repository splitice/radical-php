<?php
namespace Database\ORM;

use Database\Model\TableReferenceInstance;

class Manager {
	static function getModel(TableReferenceInstance $table){
		//cached
		$model = Cache::Get($table);
		if($model) return $model;
		
		$model = new Model($table);
		return $model;
	}
}