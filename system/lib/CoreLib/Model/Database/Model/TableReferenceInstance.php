<?php
namespace Model\Database\Model;
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
			if(!oneof($class,'\\Database\\Model\\ITable')){
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
	
	function Info(){
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
	
	/**
	 * @return \Database\SQL\SelectStatement
	 */
	function select($fields = '*'){
		return new SelectStatement($this->getTable(),$fields);
	}
	
	function __call($method,$arguments){
		return call_user_func_array(array($this->class,$method), $arguments);
	}
}