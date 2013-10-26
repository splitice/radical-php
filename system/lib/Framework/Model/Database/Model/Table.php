<?php
namespace Model\Database\Model;

use Model\Database\SQL\IMergeStatement;

use Model\Database\DynamicTypes\IDynamicValidate;
use Model\Database\DynamicTypes\INullable;
use Exceptions\ValidationException;
use Model\Database\DynamicTypes\IDynamicType;
use Model\Database\IToSQL;
use Model\Database\ORM;
use Model\Database\DBAL;
use Model\Database\SQL;
use Model\Database\SQL\Parts;

abstract class Table implements ITable, \JsonSerializable {	
	const ADAPTER = "MySQL";
	
	private $_db;
	
	/**
	 * @return \Database\IConnection
	 */
	private static function _adapter(){
		$adapter = '\\Model\\Database\\DBAL\\Adapter\\'.static::ADAPTER;
		return \DB::getConnection($adapter);
	}
	
	private static $_instance;
	private static function _instance(){
		if(self::$_instance !== null)
			return self::$_instance;
		
		$adapter = static::_adapter();
		if($adapter === null) return null;
		self::$_instance = $adapter->toInstance();
		
		return self::$_instance;
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
		if(!$this->orm->id){
			return $this->_store;
		}
		//die(var_dump($this->_store));
		foreach($this->orm->id as $k=>$v){
			$mapped = $this->orm->mappings[$v];
			if(isset($this->_store[$mapped]))
				$id[$v] = $this->_store[$mapped];
		}
		if($id) return $id;
	}
	function getIdentifyingKeys(){
		$keys = $this->orm->id;
		foreach($keys as $k=>$v){
			$keys[$k] = $this->orm->mappings[$v];
		}
		return $keys;
	}
	
	function refreshTableData(){
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
	
	protected function _handleResult($in_param){
		$in = $in_param;
		if(is_object($in)) $in = $in->toArray();

		foreach($this->orm->mappings as $k=>$v){
			if(isset($in[$k])){
				$this->$v = $in[$k];
				if(!is_array($in_param)){
					$this->_store[$v] = $in[$k];
				}
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
				if(is_object($v))
					$in[$k] = $v->getId();
			}
			//$this->_store = $in;
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
			$this->$field = $dT::fromDatabaseModel($this->$field, $value['extra'], $this, $field);
		}
	}
	
	/* Possible Implementatation - Most classes will override */
	function toSQL($in = null){
		$ret = array();
		foreach($this->orm->mappings as $k=>$mapped){
			$v = null;
			if(isset($this->$mapped)){
				$v = $this->$mapped;
				if(is_object($v) && isset($this->orm->relations[$k])){
					$v = $v->getSQLField($k);
				}
				if(is_object($v) && $v instanceof IDynamicType){
					$v = (string)$v;
				}
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
		return $ret;
	}
	
	public function jsonSerialize(){
		return $this->toSQL();
	}
	
	function update(){
		$this->Validate();
		$identifying = $this->getIdentifyingSQL();
		$values = $this->toSQL();
		foreach($identifying as $k=>$v){
			if(isset($values[$k]) && $values[$k] == $v){
				unset($values[$k]);
			}
		}
		foreach($values as $k=>$v){
			$mapped = $this->orm->mappings[$k];
			if(!isset($this->_store[$mapped])){
				if($v === null)
					unset($values[$k]);
			}elseif((string)$v == $this->_store[$mapped]){
				unset($values[$k]);
			}
		}
		
		if(count($values))
			\DB::Update($this->orm->tableInfo['name'], $values, $identifying);
	}
	
	function delete(){
		\DB::Delete($this->orm->tableInfo['name'], $this->getIdentifyingSQL());
	}
	
	public function __sleep()
	{
		if($this->_store){
			return array('_store');
		}else{
			$keys = get_object_vars($this);
			unset($keys['orm']);
			$keys = array_keys($keys);
			return $keys;
		}
	}
	
	public function __wakeup()
	{
		//Recreate ORM
		$table = TableReference::getByTableClass($this);
		$this->orm = $table->getORM();
		
		if($this->_store){
			//Re-get data
			$table = $this->RefreshTableData();
			if($table)
				$this->_handleResult($table->toSQL(true));
			//else
				//throw new \Exception("Init Error");
			
			//Initialize dynamic types
			$this->_dynamicType();
		}
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
	
	protected function _related_cache($name,$o){
		return $o;
	}
	function _related_cache_get(){
		
	}
	protected function call_get_related($className){
		//Cacheable table provides this
		$ret = $this->_related_cache_get($className);
		if($ret !== null){
			return $ret;
		}
		
		//Get Class
		try{
			//Use schema
			foreach($this->orm->references as $ref){
				if($ref['from_table']->getName() == $className){
					$select = array($ref['from_field']=>$this->getSQLField($ref['to_field']));
					return $this->_related_cache($className,$ref['from_table']->getAll($select));
				}
			}
			
			//Fallback, not schema related so try a fetch
			$relationship = TableReference::getByTableClass($className);
			if(isset($relationship)){//Is a relationship
				//Fallback to attempting to get 
				return $this->_related_cache($className,$relationship->getAll($this->getIdentifyingSQL()));
			}
		}catch(\Exception $ex){
			throw new \BadMethodCallException('Relationship doesnt exist: unable to relate');
		}
		
		throw new \BadMethodCallException('Relationship doesnt exist: unkown table');
	}
	public function fields(){
		return array_keys($this->orm->reverseMappings);
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
					$this->$actionPart = $var::fromUserModel($value,$this->orm->dynamicTyping[$actionPart]['extra'],$this);
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
				throw new \Exception('Cant get an array of something that isnt a model - '.get_called_class().'::'.$actionPart);
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
	protected static function _getAll($sql = ''){
		$obj = static::_select();
		if(is_array($sql)){
			$obj = static::_fromFields($sql);
		}elseif($sql instanceof Parts\Where){
			$obj = static::_select()
				->where($sql);
		}elseif($sql instanceof IToSQL){
			if($sql instanceof IMergeStatement){
				$obj = $sql->mergeTo(static::_select());
			}else{
				$obj = static::_select()->where($sql);
			}
		}elseif($sql){
			throw new \Exception('Invalid SQL Type');
		}
		
		return $obj;
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
		$obj = static::_getAll($sql);
		
		return new Table\TableSet($obj, get_called_class());
	}
	private static function _select(){
		return new SQL\SelectStatement(static::TABLE);
	}
	private static function _fromFields(array $fields){
		$table = TableReference::getByTableClass(get_called_class());
		$orm = ORM\Manager::getModel($table);

		if(!$orm)
			throw new \Exception('Table doesnt exist: '.$table->getName());
			
		//prefix
		$prefixedFields = array();
		foreach($fields as $k=>$f){
			if($k{0} == '*') {
				$k = static::TABLE_PREFIX.substr($k,1);
			}
			if($f instanceof Table){
				$f = $f->getId();
			}
			$prefixedFields[static::TABLE.'.'.$k] = $f;
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
			return new static($row);
		}
	}
	
	function validate(){
		foreach($this->orm->dynamicTyping as $k=>$v){
			$v = $this->$k;
			if($v instanceof IDynamicValidate)
				$v->DoValidate((string)$v,$k);
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
	
	static function new_empty(){
		return new static(array());
	}
	
	/* (non-PHPdoc)
	 * @see \Model\Database\Model\ITable::Insert()
	 */
	function insert($ignore = -1){
		$this->Validate();
		
		if($ignore instanceof InsertBuffer){
			$ignore->add($this);
			return;
		}
		
		//Build & Do SQL
		$data = $this->toSQL();
		foreach($data as $k=>$v){
			if($v === null){
				unset($data[$k]);
			}
		}
		
		$id = \DB::Insert($this->orm->tableInfo['name'],$data,is_int($ignore)?$ignore:null);
		
		foreach($data as $k=>$v){
			$this->_store[$this->orm->mappings[$k]] = $v;
		}
		
		//Is an auto incrememnt returned?
		if($id){
			$autoInc = $this->orm->autoIncrement;
			
			//Is auto increment column
			if($autoInc){
				//Set auto increment column
				$this->$autoInc = $id;
				
				//Set store
				$this->_store[$autoInc] = $id;
			}
		}
	}
	
	static function exists(){
		return \DB::tableExists($this->orm->tableInfo['name']);
	}
	static function create($data,$prefix=false){
		$res = static::fromSQL($data,$prefix);
		$res->Insert();
		return $res;
	}
}