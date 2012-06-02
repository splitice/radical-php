<?php
namespace Model\Database\Model\Table;
use Model\Database\Search\Adapter\ISearchAdapter;
use Model\Database\SQL\IStatement;
use Model\Database\SQL;
use Model\Database\DBAL;

class TableSet extends \Basic\Arr\Object\IncompleteObject {
	/**
	 * @var \Database\SQL\IStatement
	 */
	public $sql;
	public $tableClass;
	
	function __construct(IStatement $sql,$tableClass){
		$this->sql = $sql;
		$this->tableClass = $tableClass;
		
		TableCache::Add($this, $this->sql);
	}
	function Search($text,ISearchAdapter $adapter){
		$sql = clone $this->sql;
		$table = constant($this->tableClass.'::TABLE');//TODO: Cleanup
		$adapter->Filter($text, $sql, $table);
		return new static($sql,$this->tableClass);
	}
	function Filter(IStatement $merge){
		$sql = clone $this->sql;
		$merge->mergeTo($sql);
		return new static($sql,$this->tableClass);
	}
	function Delete(){
		$sql = $this->sql->mergeTo(new SQL\DeleteStatement());
		$sql->Execute();
	}
	function Update($value){
		$sql = $this->sql->mergeTo(new SQL\UpdateStatement());
		$sql->values($value);
		$sql->Execute();
	}
	function getData(){
		//Execute		
		$res = \DB::Query($this->sql);
		
		//Table'ify
		$tableClass = $this->tableClass;
		return $res->FetchCallback(function($obj) use($tableClass){
			return TableCache::Add($tableClass::fromSQL($obj));
		});
	}
	
	public function count(){
		return $this->getCount();
	}
	
	private $count;
	function getCount(){
		if($this->count !== null){
			return $this->count;
		}
		if($this->data){
			return ($this->count = count($this->data));
		}
		
		$this->count = $this->sql->getCount();
			
		return $this->count;
	}
}