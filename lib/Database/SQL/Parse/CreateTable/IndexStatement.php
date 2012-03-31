<?php
namespace Database\SQL\Parse\CreateTable;

class IndexStatement extends Internal\CreateTableStatementBase {
	protected $keys = array();
	
	function __construct($name,$type,$keys,$attributes) {
		foreach(explode(',',$keys) as $k){
			$this->keys[] = trim($k,' `');
		}
		
		parent::__construct($name,$type,$attributes);
	}
	
	/**
	 * @return the $keys
	 */
	public function getKeys() {
		return $this->keys;
	}
}