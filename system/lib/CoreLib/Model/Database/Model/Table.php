<?php
namespace Model\Database\Model;

use Model\Database\DynamicTypes\INullable;
use Exceptions\ValidationException;
use Model\Database\DynamicTypes\IDynamicType;
use Model\Database\IToSQL;
use Model\Database\ORM;
use Model\Database\DBAL;
use Model\Database\SQL;
use Model\Database\SQL\Parts;

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
		if(is_object($in)) $in = $in->toArray();

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
				if(!oneof($dT, '\\Model\\Database\\DynamicTypes\\INullable')){
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
			if(isset($this->$mapped)){
				$v = $this->$mapped;
				if(is_object($v) && isset($this->orm->relations[$k])){
					$v = $v->getSQLField($k);
				}
				if(is_object($v) && $v instanceof IDynamicType){
					$v = (string)$v;
				}
				$ret[$k] = $v;
			}
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
		\DB::Update($this->orm->tableInfo['name'], $this->toSQL(), $this->getIdentifyingSQL());
	}
	
	function Delete(){
		\DB::Delete($this->orm->tableInfo['name'], $this->getIdentifyingSQL());
	}
	
	public function __sleep()
	{
		return array('_store');
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
	private function call_get_member($actionPart,$a){
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
	}
	private function call_get_related($className){
		//Get Class
		try{
			//Use schema
			foreach($this->orm->references as $ref){
				if($ref['from_table']->getName() == $className){
					$select = array($ref['from_field']=>$this->getSQLField($ref['to_field']));
					return $ref['from_table']->getAll($select);
				}
			}
			
			//Fallback, not schema related so try a fetch
			$relationship = TableReference::getByTableClass($className);
			if(isset($relationship)){//Is a relationship
				//Fallback to attempting to get 
				return $relationship->getAll($this->getIdentifyingSQL());
			}
		}catch(\Exception $ex){
			throw new \BadMethodCallException('Relationship doesnt exist: unable to relate');
		}
		
		throw new \BadMethodCallException('Relationship doesnt exist: unkown table');
	}
	private function call_set_value($actionPart,$value){
		if(isset($this->orm->reverseMappings[$actionPart])){		
			//Is this key a dynamic type?
			if(isset($this->orm->dynamicTyping[$actionPart])){
				if(is_object($this->$actionPart) && $this->$actionPart instanceof IDynamicType){//Do we already have the key set as a dynamic type?
					if($value !== null || $this->$actionPart instanceof INullable){//can be set, set value
						$this->$actionPart->setValue($value);
					}else{//Else replace (used for null)
						$this->$actionPart = $value;
					}
				}elseif($value instanceof IDynamicType){//Have we been given a dynamic type?
					$this->$actionPart = $value;
				}elseif($value !== null || oneof($this->orm->dynamicTyping[$actionPart]['var'], 'Model\Database\DynamicTypes\INullable')){
					$var = $this->orm->dynamicTyping[$actionPart]['var'];
					$this->$actionPart = $var::fromUserModel($a[0],$this->orm->dynamicTyping[$actionPart]['extra'],$this);
				}else{//else set to null
					$this->$actionPart = null;
				}
			}else{
				$this->$actionPart = $value;
			}
			return $this;
		}else{
			throw new \BadMethodCallException('no field exists for set call');
		}
	}
	function __call($m,$a){
		if(0 === substr_compare($m,'get',0,3)){//if starts with is get*
			//get the action part
			$actionPart = substr($m,3);
			$className = $actionPart;
			$actionPart{0} = strtolower($actionPart{0});
			
			//if we have the action part from the database
			if(isset($this->orm->reverseMappings[$actionPart])){
				return $this->call_get_member($actionPart,$a);
			}elseif($actionPart{strlen($actionPart)-1} == 's'){//Get related objects (foward)
				//Remove the pluralising s from the end
				$className = substr($className,0,-1);
				
				return $this->call_get_related($className);
			}else{
				throw new \Exception('Cant get an array of something that isnt a model');
			}
		}elseif(0 === substr_compare($m,'set',0,3)){
			$actionPart = substr($m,3);
			$actionPart{0} = strtolower($actionPart{0});
			if(count($a) != 0){
				return $this->call_set_value($actionPart, $a[0]);
			}else{
				throw new \BadMethodCallException('set{X}(value) called without argument');
			}
		}
		throw new \BadMethodCallException('Not a valid function: '.$m);
	}
	
	/* Static Functions */
	/**
	 * This function gets all rows that match a specific query
	 * or all if $sql is left blank.
	 * 
	 * $sql can be an array() of tablecolumns e.g post_id
	 * $sql can be an instance of \Model\Database\SQL\Parts\Where
	 * $sql can be any class that implements IToSQL including a query built with the query builder
	 * 
	 * ```
	 * foreach(Post::getAll() as $post){
	 * 		echo $post->getId(),'<br />';
	 * }
	 * //or
	 * $posts = Post::getAll(array('category_id'=>1));
	 * echo 'Posts: ',$post->getCount(),'<br />';
	 * foreach($posts as $post){
	 * 		echo $post->getId(),'<br />';
	 * }
	 * //etc
	 * ```
	 * 
	 * @param mixed $sql
	 * @throws \Exception
	 * @return \Model\Database\Model\Table\TableSet
	 */
	static function getAll($sql = ''){
		$obj = static::_select();
		if(is_array($sql)){
			$obj = static::_fromFields($sql);
		}elseif($sql instanceof Parts\Where){
			$obj = static::_select()
				->where($sql);
		}elseif($sql instanceof IToSQL){
			$obj = $sql->mergeTo(static::_select());
		}elseif($sql){
			throw new \Exception('Invalid SQL Type');
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
		$where = new Parts\Where($prefixedFields);

		$sql = static::_select()
					->where($where);

		return $sql;
	}
	
	/**
	 * Gets a row that matches the `$fields` supplied.
	 * Returns null if nothing found.
	 * 
	 * @param array $fields
	 * @return \Model\Database\Model\Table
	 */
	static function fromFields(array $fields){
		$res = \DB::Query(static::_fromFields($fields));
		if($row = $res->Fetch()){
			return static::fromSQL($row);
		}
	}
	
	/**
	 * Gets a row from ID.
	 * If the primary key spans multiple columns then accepts
	 * input only as an of column => value etc `array('key_name1'=> ...)`
	 * Else also accepts input as a scalar value
	 * 
	 * ```
	 * $post = Post::fromId(1);
	 * ```
	 * 
	 * @param mixed $id
	 * @throws \Exception
	 * @return NULL|\Model\Database\Model\Table
	 */
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
		if($id instanceof Parts\Where){
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
	
	/**
	 * Returns a table made up of $res values.
	 * Usually used in creation/insert.
	 * 
	 * @param mixed $res
	 * @param bool $prefix array is prefixed or not
	 * @return \Model\Database\Model\Table
	 */
	static function fromSQL($res,$prefix=false){
		return new static($res,$prefix);
	}
	
	/* (non-PHPdoc)
	 * @see \Model\Database\Model\ITable::Insert()
	 */
	function Insert($ignore = -1){
		$data = $this->toSQL();
		foreach($data as $k=>$v){
			if($v === null){
				unset($data[$k]);
			}
		}
		$id = \DB::Insert($this->orm->tableInfo['name'],$data,$ignore);
		
		//Is an auto incrememnt returned?
		if($id){
			$autoInc = $this->orm->autoIncrement;
			
			//Is auto increment column
			if($autoInc){
				//Set auto increment column
				$this->$autoInc = $id;
				
				//Set store
				$this->_store[$this->orm->autoIncrementField] = $id;
			}
		}
	}
	
	static function Exists(){
		return \DB::tableExists($this->orm->tableInfo['name']);
	}
	static function Create($data,$prefix=false){
		$res = static::fromSQL($data,$prefix);
		return $res->Insert();
	}
}