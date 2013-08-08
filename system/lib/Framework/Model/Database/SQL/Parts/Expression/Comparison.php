<?php
namespace Model\Database\SQL\Parts\Expression;

use Model\Database\SQL\Parts\Internal;

class Comparison extends Internal\PartBase implements IComparison {
	private $a;
	private $b;
	private $operation;
	private $autoNull;
	private $escaped;
	
	function __construct($a,$b,$operation = '=',$autoNull = true, $escaped = false){
		$this->a = $a;
		$this->b = $b;
		$this->operation = $operation;
		$this->autoNull = $autoNull;
		$this->escaped = $escaped;
	}
	function toSQL(){
		$a = $this->a;
		if(is_string($a) && strpos($a, '(') === false && strpos($a, '`') === false){
			$at = '';
			foreach(explode('.',$a) as $v){
				if($at){
					$at .= '.';
				}
				
				$at .= '`'.$v.'`';
			}
			$a = $at;
		}
		
		$op = $this->operation;
		if($this->autoNull && $this->b === null){
			if($op == '='){
				$op = 'IS';
			}else if($op == '!=' || $op == '<>'){
				$op = 'IS NOT';
			}else{
				$op = trim(strtoupper($op));
				if($op != 'IS' && $op != 'IS NOT'){
					throw new \Exception("Invalid operation with NULL");
				}
			}
		}
		
		return $a.' '.$op.' '.($this->escaped?$this->b:\DB::E($this->b));
	}
}