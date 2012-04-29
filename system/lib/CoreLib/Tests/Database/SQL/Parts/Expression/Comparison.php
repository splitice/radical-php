<?php
namespace Tests\Database\SQL\Parts\Expression;

use Debug\Test\IUnitTest;
use Debug\Test\Unit;

class Comparison extends Unit implements IUnitTest {
	private $a;
	private $b;
	private $operation;
	
	function __construct($a,$b,$operation = '='){
		$this->a = $a;
		$this->b = $b;
		$this->operation = $operation;
	}
	function toSQL(){
		return $this->a.' '.$this->operation.' '.\DB::E($this->b);
	}
}