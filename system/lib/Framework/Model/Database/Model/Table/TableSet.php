<?php
namespace Model\Database\Model\Table;
use Model\Database\DBAL\Fetch;

use Model\Database\SQL\SelectStatement;

use Model\Database\Search\Adapter\ISearchAdapter;
use Model\Database\SQL\IStatement;
use Model\Database\SQL;
use Model\Database\DBAL;

class TableSet extends \Basic\Arr\Object\IncompleteObject {
	/**
	 * @var \Model\Database\SQL\IStatement
	 */
	public $sql;
	public $tableClass;
	
	function __construct(IStatement $sql,$tableClass){
		$this->sql = $sql;
		$this->tableClass = $tableClass;
	}
	function search($text,ISearchAdapter $adapter){
		$sql = clone $this->sql;
		$table = constant($this->tableClass.'::TABLE');//TODO: Cleanup
		$adapter->Filter($text, $sql, $table);
		return new static($sql,$this->tableClass);
	}
	function filter(IStatement $merge){
		$sql = clone $this->sql;
		$merge->mergeTo($sql);
		return new static($sql,$this->tableClass);
	}
	function delete(){
		$sql = $this->sql->mergeTo(new SQL\DeleteStatement());
		$sql->Execute();
	}
	function update($value){
		$sql = $this->sql->mergeTo(new SQL\UpdateStatement());
		$sql->values($value);
		$sql->Execute();
	}
	function getData(){
		//Execute		
		$res = \DB::Query($this->sql);
		
		//Table'ify
		return $res->FetchCallback(array($this->tableClass,'fromSQL'));
	}
	function reset(){
		$this->data = null;
	}
	public function count(){
		return $this->getCount();
	}
	
	private $count;
	function setSQLCount(SelectStatement $sql){
		$this->count = $sql;
	}
	function getCount(){
		if($this->count !== null){
			if($this->count instanceof SelectStatement){
				$this->count = $this->count->query()->fetch(Fetch::FIRST);
			}
			return $this->count;
		}
		if($this->data){
			return ($this->count = count($this->data));
		}
		
		$this->count = $this->sql->getCount();
			
		return $this->count;
	}
}