<?php
namespace Tests\Database\SQL\Parts\Expression;

use Debug\Test\IUnitTest;
use Debug\Test\Unit;

class Fulltext extends Unit implements IUnitTest {
	protected $text;
	protected $fields;
	protected $boolean;
	
	function __construct($text,$fields,$boolean = false){
		$this->text = $text;
		if(!is_array($fields)){
			$fields = array($fields);
		}
		$this->fields = $fields;
		$this->boolean = $boolean;
	}
	
	function toSQL(){
		$db = \DB::getInstance();
		$sql = 'MATCH ('.implode(',',$this->fields).') ';
		$sql .= 'AGAINST ('.$db->Escape($this->text);
		if($this->boolean){
			$sql .= ' IN BOOLEAN MODE';
		}
		$sql .= ')';
		return $sql;
	}
}