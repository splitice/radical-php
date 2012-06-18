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
		$this->operation = $operation;
	}
	function toSQL(){
		$a = $this->a;
		if(is_string($a) && strpos($a, '(') === false && strpos($a, '`') === false){
			$a = '`'.$a.'`';
		}
		return $a.' '.$this->operation.' '.\DB::E($this->b);
	}
}