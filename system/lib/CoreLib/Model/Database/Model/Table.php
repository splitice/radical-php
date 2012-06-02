<?php
namespace Database\Model;

use Exceptions\ValidationException;
use Database\DynamicTypes\IDynamicType;
use Database\IToSQL;
use Database\ORM;
use Database\DBAL;
use Database\SQL;

abstract class Table implements ITable, \JsonSerializable {	
	static function _idString($id){
		if(is_object($id)){
			$id = (array)$id;
		}
		if(is_array($id)) {
			ksort($id);
			$id = implode('|',$id);
		}
		$id .= '|'.get_called_class();
		return $id;
	}
	public function getIdKey(){
		return static::_idString($this->getId());
	}
	protected $_id;
	function getId(){
		//Check if already done
		if($this->_id !== null){
			return $this->_id;
		}
		
		$orm = $this->orm;
		
		//Build ID Array
		$id = array();
		foreach($orm->id as $key){
			//Use the object mapped name to get the field value
			$value = $this->{$orm->mappings[$key]};
			
			//if is referenced link then resolve to field value
			if(is_object($value) && $orm->relations[$key]){
				$value = $value->getSQLField($key);
			}
			
			//store in id as DBName=>value
			$id[$key] = $value;
		}
		
		//Make string if there is only one
		if(count($id) === 1){
			$id = $id[$key];
		}
	
		//Cache to _id
		$this->_id = $id;
	
		return $id;
	}
	function getIdentifyingSQL(){
		$id = array();
		foreach($this->orm->id as $k=>$v){
			$id[$v] = $this->_store[$this->orm->mappings[$v]];
		}
		if($id) return $id;
		throw new \Exception('Invalid ID SQL');
	}
	function getIdentifyingKeys(){
		$keys = $this->orm->id;
		foreach($keys as $k=>$v){
			$keys[$k] = $this->orm->mappings[$v];
		}
		return $keys;
	}
	
	function RefreshTableData(){
		return static::fromId(static::getIdentifyingSQL());
	}
	function setSQLField($field,$value){
		//Check can map
		if(!isset($field)){
			throw new \Exception('SQL field '.$field.' invalid');
		}
		
		//Get field name
		$field = $this->orm->mappings[$field];
		
		//Validate
		if(!$this->orm->validation->Validate($field, $value)){
			throw new ValidationException('Couldnt set '.static::TABLE.'.'.$field.' to '.$value);
		}
		
		//Set field
		$vRef = &$this->$field;
		if($vRef instanceof IDynamicType){
			$vRef->setValue($value);
		}else{
			$vRef = $value;
		}
	}
	function getSQLField($field,$object = false) {
		//Map non prefixed
		if($field{0} == '*'){
			$field = static::TABLE_PREFIX.substr($field,1);
		}
		
		//Check can map
		if(!isset($this->orm->mappings[$field])){
			throw new \Exception('SQL field '.$field.' invalid');
		}

		//Get field name
		$translated = $this->orm->mappings[$field];
		
		//Get data
		$ret = $this->$translated;
		
		//Want an object?
		if($object && isset($this->orm->relations[$field]) && !is_object($ret)){
			$relation = $this->orm->relations[$field];
			
			$c = $relation->getTableReference();
			if($c){
				$c = $c->getClass();
				$this->$translated = $ret = $c::fromId($ret);
			}
		}
		if(!$object && is_object($ret) && !($ret instanceof IDynamicType)){
			$ret = $ret->getId();
		}
		return $ret;
	}
	
	protected function _handleResult($in){
		if(is_object($in)) $in = $in->asArray();

		foreach($this->orm->mappings as $k=>$v){
			if(isset($in[$k])){
				$this->_store[$v] = $this->$v = $in[$k];
			}
		}
	}
	
	public $orm;
	protected $_store = array();
	function __construct($in = array(),$prefix = false){
		//Setup object with table specific data
		$table = TableReference::getByTableClass($this);
		$this->orm = $table->getORM();
		
		//Load data into table
		if($in instanceof DBAL\Row || $prefix){
			$this->_handleResult($in);
		}elseif(is_array($in)){
			foreach($in as $k=>$v){
				$this->$k = $v;
			}
			$this->_store = $in;
		}else{
			throw new \Exception('Cant create table with this data');
		}
		$this->_dynamicType();
	}
	
	private function _dynamicType(){
		//Construct dynamic types
		foreach($this->orm->dynamicTyping as $field=>$value){
			$dT = $value['var'];
			if($this->$field === null){
				if(!oneof($dT, '\\Database\\DynamicTypes\\INullable')){
					continue;
				}
			}
			$this->$field = $dT::fromDatabaseModel($this->$field,$value['extra'],$this);
		}
	}
	
	/* Possible Implementatation - Most classes will override */
	function toSQL($in = null){
		$ret = array();
		foreach($this->orm->mappings as $k=>$mapped){
			$v = $this->$mapped;
			if(is_object($v) && isset($this->orm->relations[$k])){
				$v = $v->getSQLField($k);
			}
			if(is_object($v) && $v instanceof IDynamicType){
				$v = (string)$v;
			}
			$ret[$k] = $v;
		}
		return $ret;
	}
	
	function toExport(){
		$data = $this->toSQL();
		$ret = array();
		foreach($this->orm->mappings as $k=>$v){
			$ret[$v] = $data[$k];
		}
		return $v;
	}
	
	public function jsonSerialize(){
		return $this->toSQL();
	}
	
	function Update(){
		//die(var_dump($this->toSQL()));
		
		\DB::Update($this->orm->tableInfo['name'], $this->toSQL(), $this->getIdentifyingSQL());
	}
	
	function Delete(){
		\DB::Delete($this->orm->tableInfo['name'], $this->getIdentifyingSQL());
	}
	
	public function __sleep()
	{
		return array('_store');//array_values($this->getIdentifyingKeys());
	}
	
	public function __wakeup()
	{
		//Recreate ORM
		$table = TableReference::getByTableClass($this);
		$this->orm = $table->getORM();

		//Re-get data
		$this->_handleResult($this->RefreshTableData()->toSQL());
		$this->_dynamicType();
	}
	
	function __toString(){
		$id = $this->getId();
		if(is_array($id)) $id = implode('|',$id);
		return $id;
	}
	
	function __call($m,$a){
		if(0 === substr_compare($m,'get',0,3)){//if starts with is get*
			//get the action part
			$actionPart = substr($m,3);
			$className = $actionPart;
			$fullClassName = \ClassLoader::getProjectSpace('DB\\'.$className);
			$actionPart{0} = strtolower($actionPart{0});
			
			//if we have the action part from the database
			if(isset($this->orm->reverseMappings[$actionPart])){
				$relations = $this->orm->relations;
				$dbName = $this->orm->reverseMappings[$actionPart];
				if(isset($relations[$dbName]) && !is_object($this->$actionPart)){
					$class = $relations[$dbName]->getTableClass();
					if(isset($a[0]) && $a[0] == 'id'){
						$ret = &$this->$actionPart;
						if(is_object($ret)){
							$ret = $this->getId();
						}
					}else{
						$this->$actionPart = $class::fromId($this->$actionPart);
					}
				}
				if(isset($a[0]) && $a[0] == 'id' && is_object($this->$actionPart)){
					$ret = $this->$actionPart->getId();
				}else{
					$ret = &$this->$actionPart;
				}
				return $ret;
			}elseif($actionPart{strlen($actionPart)-1} == 's'){//Get related objects (foward)
				//Remove the pluralising s from the end
				$className = substr($className,0,-1);
				
				//Get Class
				$relationship = TableReference::getByTableClass($className);
				if(isset($relationship)){//Is a relationship
					$class = $relationship->getClass();
					return $class::getAll($this->getIdentifyingSQL());
				}else{
					throw new \Exception('Cant get an array of something that isnt a relationship');
				}
			}
		}elseif(0 === substr_compare($m,'set',0,3)){
			$actionPart = substr($m,3);
			$actionPart{0} = strtolower($actionPart{0});
			if(isset($this->orm->reverseMappings[$actionPart])){
				if(!isset($a[0])){
					throw new \BadMethodCallException('set{X}(value) called without argument');
				}
				$this->$actionPart = $a[0];
				return $this;
			}
		}
		throw new \BadMethodCallException('Not a valid function: '.$m);
	}
	
	/* Static Functions */
	static function getAll($sql = ''){
		$obj = static::_select();
		if(is_array($sql)){
			$obj = static::_fromFields($sql);
		}elseif($sql instanceof \Database\SQL\Parts\Where){
			$obj = static::_select()
				->where($sql);
		}elseif($sql instanceof \Database\IToSQL){
			$obj = $sql->mergeTo(static::_select());
		}elseif($sql){
			debug_print_backtrace();
			die(var_dump($sql));
		}
		
		$cached = Table\TableCache::Get($obj);
		if($cached){		
			return $cached;
		}else{
			return new Table\TableSet($obj, get_called_class());
		}
	}
	private static function _select(){
		return new SQL\SelectStatement(static::TABLE);
	}
	private static function _fromFields(array $fields){
		$orm = ORM\Manager::getModel(TableReference::getByTableClass(get_called_class()));

		//prefix
		$prefixedFields = array();
		foreach($fields as $k=>$f){
			if($k{0} == '*') {
				$k = static::TABLE_PREFIX.substr($k,1);
			}
			$prefixedFields[$k] = $f;
		}
		
		//Build SQL
		$where = new \Database\SQL\Parts\Where($prefixedFields);

		$sql = static::_select()
					->where($where);

		return $sql;
	}
	static function fromFields(array $fields){
		$res = \DB::Query(static::_fromFields($fields));
		if($row = $res->Fetch()){
			return static::fromSQL($row);
		}
	}
	
	static function fromId($id){
		//Check Cache
		$cache_string = static::_idString($id);
		$ret = Table\TableCache::Get($cache_string);
		
		//If is cached
		if($ret){
			return $ret;
		}
		
		$orm = ORM\Manager::getModel(TableReference::getByTableClass(get_called_class()));
		
		//Base SQL
		$sql = static::_select();
		
		//Build
		if($id instanceof \Database\SQL\Parts\Where){
			$sql->where($id);
		}else{
			$idk = $orm->id;
			
			if(is_array($id)){
				if(count($id) != count($idk)){
					throw new \Exception('Number of inputs doesnt match '.count($id).' != '.count($idk));
				}
				if(isset($id[0])){
					$idNew = array();
					foreach($idk as $k=>$v){
						$idk[$k] = $v;
					}
					$id = $idk;
				}
				
				$sql = static::_fromFields($id);
			}else{
				//Input = String, Needed Array
				if(count($orm->id) > 1){
					throw new \Exception('Needs more than one value for the ID of '.$this->orm->tableInfo['name']);
				}
				$sql->where(array($idk[0]=>$id));
			}
		}
		
		$res = \DB::Query($sql);
		if($row = $res->Fetch()){
			$r = new static($row);
			Table\TableCache::Add($r);
			return $r;
		}
	}
	static function fromSQL($res,$prefix=false){
		return new static($res,$prefix);
	}
	function Insert($ignore = -1){
		$data = $this->toSQL();
		foreach($data as $k=>$v){
			if($v === null){
				unset($data[$k]);
			}
		}
		\DB::Insert($this->orm->tableInfo['name'],$data,$ignore);
	}
	static function Exists(){
		return \DB::tableExists($this->orm->tableInfo['name']);
	}
	static function Create($data,$prefix=false){
		$res = static::fromSQL($data,$prefix);
		return $res->Insert();
	}
}