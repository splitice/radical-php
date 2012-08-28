<?php
namespace Web\Session\Authentication\Source;

use Model\Database\DynamicTypes\Password;
use Model\Database\Model\TableReferenceInstance;

class MultipleDatabase extends NullSource {
	const FIELD_USERNAME = '*username';
	const FIELD_PASSWORD = '*password';
	private $tables = array();

	function __construct(){
		$this->tables = func_get_args();
		parent::__construct();
	}
	protected function getFields($username){
		return array(static::FIELD_USERNAME=>$username);
	}

	function login($username,$inPassword){
		foreach($this->tables as $table){
			$class = $table->getClass();
	
			$data = $this->getFields($username);
	
			$res = $class::fromFields($data);

			if($res){
				$password = $res->getSQLField(static::FIELD_PASSWORD);
				if($password){
					if($password instanceof Password){
						if($password->Compare($inPassword)){
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