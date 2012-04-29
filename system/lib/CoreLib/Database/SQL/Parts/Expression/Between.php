<?php
namespace Database\SQL\Parts\Expression;

use Database\DBAL\Adapter\Connection;
use Database\SQL\Parts\Internal;

class Between extends Internal\PartBase implements IComparison {
	private $a;
	private $b;
	
	function __construct($a,$b){
		$this->a = $a;
		$this->b = $b;
	}
	function E(Connection $db,$value){
		if(is_object($value)){
			if($value instanceof IToSQL){
				$value = $value->toSQL();
			}
		}
		return $db->Escape($value);
	}
	function toSQL(){
		$db = \DB::getInstance();
		return ' BETWEEN '.$this->E($db,$this->a).' AND '.$this->E($db,$this->b);
	}
}