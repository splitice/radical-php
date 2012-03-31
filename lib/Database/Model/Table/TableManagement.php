<?php
namespace Database\Model\Table;
use Database\Model\TableReferenceInstance;
use Database\SQL;
use Database\DBAL\Fetch;

class TableManagement extends \Core\Object {
	protected $table;
	function __construct(TableReferenceInstance $table){
		$this->table = $table;
	}
	
	private $createTable;
	private function getCreateTable(){
		if($this->createTable){
			return $this->createTable;
		}
		$res = new SQL\ShowCreateTable($this->table);
		$createTable = $res->Execute()->Fetch(Fetch::NUM);
		$createTable = $createTable[1];
		
		$this->createTable = new SQL\Parse\CreateTable($createTable);
		
		return $this->createTable;
	}
	function getRelations(){
		$ct = $this->getCreateTable();
		return $ct->relations->asArray();
	}
	function getColumns(){
		$ct = $this->getCreateTable();
		return $ct->asArray();
	}
	/**
	 * @return the $table
	 */
	public function getTable() {
		return $this->table;
	}

}