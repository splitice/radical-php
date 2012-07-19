<?php
namespace Model\Database\Model\Table;
use Model\Database\Search\Adapter\ISearchAdapter;
use Model\Database\SQL\IStatement;
use Model\Database\SQL;
use Model\Database\DBAL;

class CacheableTableSet extends TableCache {
	function __construct(IStatement $sql,$tableClass){
		parent::__construct($sql,$tableClass);
		TableCache::Add($this, $this->sql);
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
}