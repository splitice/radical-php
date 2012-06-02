<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace Debug\Test;
use Core\Provider;

/**
 * The Test Controller for running the html version of the test suite
 *
 */
class Controller {
	static function RunUnitTests(){
		$results = array();
		$tests = Provider::Find('interface.Debug.Test.IUnitTest',true);
		foreach($tests as $class){
			$results[$class] = static::RunUnit($class);
		}
		return $results;
	}
	static function RunUnit($class){
		$r = new \ReflectionClass($class);
		$methods = array();
		foreach($r->getMethods() as $m){
			$methodName = $m->name;
			if(substr($methodName,0,4) == 'test'){
				$methods[] = $methodName;
			}
		}
		$ret = array();
		foreach($methods as $method){
			$ret[$method] = static::RunTest($class,$method);
		}
		return $ret;
	}
	static function RunTest($class,$method){
		$obj = new $class;
		try {
			$return = $obj->run($method);
		}catch(\Exception $ex){
			return $ex;
		}
		return $return;
	}
}
