<?php
namespace Database\Model\Table;
use Database\SQL\IStatement;
use Database\SQL;
use Database\DBAL;

class TableSet extends \Basic\ArrayLib\Object\IncompleteObject {
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
		$r = $res->FetchCallback(array($this->tableClass,'fromSQL'));
			
		//Set already existing objects to stored references, if they exist, else store them as Id'ed
		foreach($r as $k=>$v){
			TableCache::Add($v);
		}
			
		return $r;
	}
	
	private $count;
	function getCount(){
		if($this->count !== null){
			return $this->count;
		}
		if($this->data){
			return count($this->data);
		}
		
		//Check for entry
		$count = clone $this->sql;
		$count->fields('COUNT(*)');
		
		$res = \DB::Query($count);
		$this->count = $res->Fetch(DBAL\Fetch::FIRST,new \Cast\Integer());
			
		return $this->count;
	}
}