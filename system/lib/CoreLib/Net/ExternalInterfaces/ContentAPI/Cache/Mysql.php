<?php
namespace Net\ExternalInterfaces\ContentAPI\Cache;

use Database\Model\DynamicTableReference;

use Database\Model\DynamicTableInstance;
use Database\DBAL;

class Mysql extends DynamicTableInstance {
	const CACHE_TIME = 360000;//~100 hours
	
	function __construct($data){
		if(is_object($data)){
			if($data instanceof DBAL\Row){
				foreach($data as $k=>$v){
					if($v !== null && $k != 'ca_ttl') $data[$k] = unserialize($v);
				}
			}
		}
		parent::__construct($data);
	}
	private static function _serialize($v){
		if(is_numeric($v) && !is_bool($v)){
			if($v == (float)$v){
				if($v == (int)$v){
					$v = (int)$v;
				}else{
					$v = (float)$v;
				}
			}
		}
		return serialize($v);
	}
	function toSQL($in=null){
		if(!$in) $in = get_object_vars($this);
	
		foreach($in as $k=>$v){
			if($v instanceof DynamicTableInstance || isset(static::$_relationships[$k])){
				//TODO: Dont serialize, relate
			}else{
				$in[$k] = static::_serialize($v);
			}
		}
		
		if(static::CACHE_TIME == 0){
			$in['ttl'] = 0;
		}else{
			$in['ttl'] = time()+static::CACHE_TIME;
		}
		
		return parent::toSQL($in);
	}
	
	static function fromId($id){
		return parent::fromId(static::_serialize($id));
	}
	static function fromFields(array $fields){
		$s = array();
		foreach($fields as $k=>$v){
			$s['*'.$k] = static::_serialize($v);
		}
		return parent::fromFields($s);
	}
	static private function getTable($class){
		$dt = new DynamicTableReference(static::getTableName($class), 'ca_',get_called_class());
		$dt->addId('id', 'varchar(255)');
		foreach($class::getFields() as $field){
			$dt->addField($field, 'blob');
		}
		$dt->addField('ttl', 'int unsigned');
		
		$dt->EnsureExists(true);
		return $dt;
	}
	static function Get($class, $id){
		$table = static::getTable($class);
		$obj = $table->fromId($id);
		if($obj){
			$data = $obj->toExport();
			
			return $data;
		}
	}
	static function Set(\Net\ExternalInterfaces\ContentAPI\Modules\Internal\ModuleBase $module, array $data){
		$id = $module->getId();
		
		$table = static::getTable(get_class($module));
		
		$data['id'] = $module->getId();
		$obj = $table->fromSQL($data);
		
		$obj->Insert();
	}
	static function TTL(){
		foreach(\ClassLoader::getNSExpression('\\Net\ExternalInterfaces\\ContentAPI\\Modules\\*') as $module){
			$table = static::getTable($module);
			if($table->Exists()){
				foreach($table->getAll(' WHERE ca_ttl<='.time()) as $k){
					$k->Delete();
				}
			}
		}
	}
}