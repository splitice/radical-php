<?php
namespace Model\Database\ORM;

use Model\Database\Model\TableReferenceInstance;

class Manager {
	static function getModel(TableReferenceInstance $table, $data = true){
		//cached
		$model = Cache::Get($table);
		if($model) return $model;
		
		$model = new Model($table);
		
		if($data)
			$model = $model->toModelData();
		
		return $model;
	}
}