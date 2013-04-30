<?php
namespace Model\Database\Model;
use Model\Database\SQL\InsertStatement;

use Model\Database\SQL\UpdateStatement;

use Model\Database\SQL\UnLockTable;

use Model\Database\SQL\LockTable;

use Model\Database\SQL\SelectStatement;

use Model\Database\ORM;

class TableReferenceInstance extends \Core\Object {
	protected $class;
	
	function __construct($class){
		if($class instanceof ITable){
			$class = get_class($class);
		}else{
			if(!class_exists($class)){
				$class2 = \Core\Libraries::getProjectSpace('DB\\'.$class);
				if(class_exists($class2)){
					$class = $class2;
				}else{
					throw new \Exception($class.' class does not exist');
				}
			}
			if(!oneof($class,'\\Model\\Database\\Model\\ITable')){
				throw new \Exception($class.' is not a Database Table object');
			}
		}
		$this->class = $class;
	}
	
	function getTableManagement(){
		//Generate Table Management Class name
		$class = explode('\\',$this->class);
		$count = count($class);
		$class[$count] = $class[$count-1];
		$class[$count-1] = 'Management';
		$class = implode('\\',$class);
		
		//If it exist, return instance of class
		if(class_exists($class)){
			return new $class($this);
		}
		
		//Else return instance of default table manager
		return new Table\TableManagement($this);
	}
	
	function getORM(){
		return ORM\Manager::getModel($this);
	}
	
	/**
	 * @return the $class
	 */
	public function getClass() {
		return $this->class;
	}
	
	function getName(){
		$e = explode('\\',$this->class);
		return array_pop($e);
	}
	
	function __toString(){
		return $this->class;
	}
	
	function info(){
		$class = $this->class;
		$info = array();
		$info['name'] = $class::TABLE;
		$info['prefix'] = $class::TABLE_PREFIX;
		
		return $info;
	}
	
	function getTable(){
		$class = $this->class;
		return $class::TABLE;
	}
	function getPrefix(){
		$class = $this->class;
		return $class::TABLE_PREFIX;
	}
	
	function lock($mode = 'write'){
		$sql = new LockTable($this, $mode);
		$sql->Execute();
	}
	function unlock(){
		$sql = new UnLockTable();
		$sql->Execute();
	}
	function exists(){
		$sql = 'show tables like '.\DB::E($this->getTable());
		$res = \DB::Q($sql);
		if($res->Fetch()) {
			return true;
		}
		return false;
	}
	/**
	 * @return \Model\Database\SQL\SelectStatement
	 */
	function select($fields = '*',$type = ''){
		$class = '\\Model\\Database\\SQL\\'.$type.'SelectStatement';
		return new $class($this->getTable(),$fields);
	}
	
	function update($values = array(),$where = array()){
		return new UpdateStatement($this->getTable(),$values,$where);
	}
	function insert($values = array()){
		return new InsertStatement($this->getTable(),$values);
	}
	
	function __call($method,$arguments){
		return call_user_func_array(array($this->class,$method), $arguments);
	}
}