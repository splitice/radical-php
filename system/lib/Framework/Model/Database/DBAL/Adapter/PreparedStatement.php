<?php

namespace Model\Database\DBAL\Adapter;

class PreparedStatement implements \ArrayAccess {
	const UNBUFFERED = 0;
	const BUFFERED = 1;
	
	private $statement;
	private $db;
	function __construct($sql,$db){
		$mysqli = $db->Connect();
		$this->statement = $mysqli->prepare($sql);
		$this->db = $db;
	}
	
	private $pointer = 0;
	function bind($value,$type = null, $offset = null){
		if($offset == null){
			$offset = $this->pointer;
		}
		$this->statement->bind_param($type,$value);
		$this->pointer = $offset + 1;
	}
	
	function execute($mode = self::UNBUFFERED){
		$this->db->buffer();
		
		$class = '\\Model\\Database\\DBAL\\Adapter\\Prepared\\';
		if($mode == self::UNBUFFERED){
			$class .= 'UnBuffered';
		}elseif($mode == self::UNBUFFERED){
			$class .= 'Buffered';
		}
		
		$this->_bind();
		//Weakmap so we know when to create a new statement.
		
		return new $class($this->statement,$this);
	}
	
	function __destruct(){
		$this->statement->close();
	}
	
	/* ArrayAccess */
	public function offsetSet($offset, $v) {
		$this->bind($v,$offset);
	}
	public function offsetExists($offset) {
		if(!is_numeric($offset) || $offset < 0 || $offset > $this->statement->param_count)
			return false;
		
		return true;
	}
	public function offsetUnset($offset) {
		throw new \Exception('Read-Only array');
	}
	public function offsetGet($offset) {
		throw new \Exception('Read-Only array');
	}
}