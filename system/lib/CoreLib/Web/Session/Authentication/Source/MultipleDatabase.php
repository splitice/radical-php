<?php
namespace Web\Session\Authentication\Source;

use Database\DynamicTypes\Password;
use Database\Model\TableReferenceInstance;

class MultipleDatabase extends NullSource {
	const FIELD_USERNAME = '*username';
	const FIELD_PASSWORD = '*password';
	private $tables = array();

	function __construct(){
		$this->tables = func_get_args();
		parent::__construct();
	}
	protected function getFields($username,$password,$table){
		return array(static::FIELD_USERNAME=>$username);
	}

	function Login($username,$password){
		foreach($this->tables as $table){
			$class = $table->getClass();
	
			$data = $this->getFields($username,$password,$table);
	
			$res = $class::fromFields($data);

			if($res){
				$password = $res->getSQLField(static::FIELD_PASSWORD);
				if($password){
					if($password instanceof Password){
						if($password->Compare($password)){
							if($res){
								\Web\Session::$data['user'] = $res;
								return true;
							}
						}
					}else{
						throw new \Exception(static::FIELD_PASSWORD.' must be a DynamicType of Password');
					}
				}
			}
		}
		return false;
	}
}