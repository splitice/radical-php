<?php
namespace Database\SQL\Parts\Expression;

use Database\SQL\Parts\Internal;

class Fulltext extends Internal\PartBase {
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