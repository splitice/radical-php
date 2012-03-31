<?php
namespace Database\Model;
use Database\ORM;

class TableReferenceInstance extends \Core\Object {
	protected $class;
	
	function __construct($class){
		if($class instanceof ITable){
			$class = get_class($class);
		}else{
			if(!class_exists($class)){
				$class2 = \ClassLoader::getProjectSpace('DB\\'.$class);
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
		
		//If it doesnt exist, use default
		if(!class_exists($class)){
			$class = '\\Database\\TableManagement';
		}
		
		//Return instance
		return new $class($this);
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
	
	function __call($method,$arguments){
		return call_user_func_array(array($this->class,$method), $arguments);
	}
}