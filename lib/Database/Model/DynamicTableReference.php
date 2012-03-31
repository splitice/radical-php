<?php
namespace Database\Model;

class DynamicTableReference extends TableReference {
	protected $_tableName;
	protected $_tablePrefix;
	protected $_tableId;
	private $_data;
	
	function toSQL($in=null){
		if(!$in) $in = get_object_vars($this);
		
		unset($in['_tableName'],$in['_tablePrefix'],$in['_tableId'],$in['_data'],$in['_extra']);
		
		return parent::toSQL($in);
	}
	
	function toExport(){
		$in = get_object_vars($this);
		
		unset($in['_tableName'],$in['_tablePrefix'],$in['_tableId'],$in['_data'],$in['_extra']);
		
		return $in;
	}
	
	function __construct($data){
		$this->_data = $data;
	}
	
	function getId(){
		$a = $this->_tableId;
		if(is_array($a)){
			$ret = array();
			foreach($this->_tableId as $v){
				$ret[$v] = $this->$v;
			}
			return $ret;
		}
		return $this->$a;
	}
	
	private static function _obj(){
		foreach(debug_backtrace(true) as $o){
			if(isset($o['object']) && ($o['object'] instanceof DynamicTableReference || $o['object'] instanceof DynamicTableReference)){
				return $o['object'];
			}
		}
		throw new \Exception('Couldnt find DyanmicTable data');
	}
	public static $__tablePrefix;
	static function _tablePrefix(){
		if(static::$__tablePrefix) return static::$__tablePrefix;
		return (static::$__tablePrefix = static::_obj()->_tablePrefix);
	}
	public static $__tableId;
	static function _tableId(){
		if(static::$__tableId) return static::$__tableId;
		$t = static::_obj();
		if($t instanceof DynamicTableReference){
			return (static::$__tableId = $t->_tableId);
		}else{
			return (static::$__tableId = 'id');
		}
	}
	public static $__tableName;
	static function _tableName(){
		if(static::$__tableName) return static::$__tableName;
		return (static::$__tableName = static::_obj()->_tableName);
	}
	
	function _Setup(DynamicTableReference $table){
		$this->_tableName = $table->getTableName();
		$this->_tableId = $table->getTableId();
		$this->_tablePrefix = $table->getTablePrefix();

		if(isset($this->_data)){
			//Create struct
			foreach(array_merge($table->getIds(),$table->getFields()) as $id=>$type){
				$id = self::_translateName($id,false);
				$this->$id = null;
			}
			
			//Run the constructor with temp data
			parent::__construct($this->_data);
			
			//Clear Temp
			unset($this->_data);
		}
	}
	
	function _get($var){
		$var{0} = strtolower($var{0});
		return $this->$var;
	}
	
	function __call($method,$arguments){
		if(substr_compare($method, 'get', 0, 3) === 0){
			return $this->_get(substr($method,3));
		}
	}
}