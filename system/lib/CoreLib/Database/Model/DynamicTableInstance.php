<?php
namespace Database\Model;

use Database\SQL\ShowCreateTable;

use Database\SQL\CreateTable;

class DynamicTableInstance {
	public $_tableName;
	public $_tablePrefix;
	private $class;
	
	protected $id = array();
	protected $field = array();
	
	function __construct($tableName,$tablePrefix,$class = '\\Database\\DynamicTableInstance'){
		$this->_tableName = $tableName;
		$this->_tablePrefix = $tablePrefix;
		$this->class = $class;
	}
	
	/**
	 * @return the $tableName
	 */
	public function getTableName() {
		return $this->_tableName;
	}

	/**
	 * @return the $tablePrefix
	 */
	public function getTablePrefix() {
		return $this->_tablePrefix;
	}

	/**
	 * @return the $tableId
	 */
	public function getTableId() {
		$ids = array_keys($this->id);
		if(count($ids) == 1){
			return $ids[0];
		}
		return $ids;
	}

	/**
	 * @return the $id
	 */
	public function getIds() {
		return $this->id;
	}

	/**
	 * @return the $field
	 */
	public function getFields() {
		return $this->field;
	}

	function addId($name,$type){
		unset($this->field[$name]);
		$this->id[$name] = $type;
	}
	function addField($name,$type){
		unset($this->id[$name]);
		$this->field[$name] = $type;
	}
	
	/* Dynamic Static methods */
	/*function fromFields(array $fields){
		$class = $this->class;
		$r = $class::fromFields($fields);
		if($r) $r->_Setup($this);
		return $r;
	}
	function fromId($id){
		$class = $this->class;
		$r = $class::fromId($id);
		if($r) $r->_Setup($this);
		return $r;
	}
	function fromSQL($res){
		$class = $this->class;
		$r = $class::fromSQL($res);
		if($r) $r->_Setup($this);
		return $r;
	}
	function getAll($sql = ''){
		$class = $this->class;
		$ret = $class::getAll($sql);
		foreach($ret as $r){
			$r->_Setup($this);
		}
		return $ret;
	}
	function getSQL($sql = ''){
		$class = $this->class;
		$r = $class::getSQL($sql);
		if($r) $r->_Setup($this);
		return $r;
	}
	function getCount($sql=''){
		$class = $this->class;
		return $class::getCount($sql);
	}*/
	
	function Exists(){
		return \DB::TableExists($this->_tableName);
	}
	
	function Create(){
		$createTable = new CreateTable($this->_tableName);
		foreach(array_merge($this->id,$this->field) as $name=>$type){
			$createTable->addFeild($this->_tablePrefix.$name,$type);
		}
		
		$idx = array();
		foreach(array_keys($this->id) as $v){
			$idx[] = $this->_tablePrefix.$v;
		}
		$createTable->addIndex('PRIMARY', $idx);
		
		$sql = $createTable->toSQL();
		
		\DB::Q($sql);
	}
	
	function ValidateFields(){
		$table = \Database\SQL\Parse\CreateTable::fromTable($this->_tableName);
		$data = $table->asArray();
		
		foreach(array_merge($this->id,$this->field) as $name=>$type){
			$k = $this->_tablePrefix.$name;
			if(!isset($data[$k])){
				return false;
			}
			unset($data[$k]);
		}
			
		if($data){
			return false;
		}
		
		return true;
	}
	
	function EnsureExists($drop = false){
		//Check if we need to do anything
		if($this->Exists()){
			if($this->ValidateFields()){
				return true;
			}else{
				if($drop){
					\DB::Q('DROP TABLE '.$this->_tableName);
				}
			}
		}
		
		//Create
		$this->Create();
		
		return false;
	}
}