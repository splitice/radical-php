<?php
namespace Web\Session\Handler\Internal;

abstract class HandlerBase extends \Core\Object implements ISessionHandler {
	function __construct(){
		\Web\Session::Init($this);
		foreach(\ClassLoader::getNSExpression('\\Web\\Session\\Extra\\*') as $class){
			new $class($this);
		}
	}
}