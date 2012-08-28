<?php
namespace Model\Database\SQL\Parts\Expression;

use Model\Database\IToSQL;
use Model\Database\DBAL\Adapter\Connection;
use Model\Database\SQL\Parts\Internal;

class Between extends Internal\PartBase implements IComparison {
	private $a;
	private $b;
	
	function __construct($a,$b){
		$this->a = $a;
		$this->b = $b;
	}
	function e(Connection $db,$value){
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