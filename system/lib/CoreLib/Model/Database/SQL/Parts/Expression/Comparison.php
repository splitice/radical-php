<?php
namespace Model\Database\SQL\Parts\Expression;

use Model\Database\SQL\Parts\Internal;

class Comparison extends Internal\PartBase implements IComparison {
	private $a;
	private $b;
	private $operation;
	
	function __construct($a,$b,$operation = '='){
		$this->a = $a;
		$this->b = $b;
		if(is_array($b)){
			debug_print_backtrace();
			exit;
		}
		$this->operation = $operation;
	}
	function toSQL(){
		$a = $this->a;
		if(strpos($a, '(') === false){
			$a = '`'.$a.'`';
		}
		return $a.' '.$this->operation.' '.\DB::E($this->b);
	}
}