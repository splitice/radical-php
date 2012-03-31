<?php
namespace Database\Model\Pagination;

use Net\URL\Pagination\IPaginator;

use Net\URL\Pagination\Template\IPaginationTemplate;
use Database\SQL;
use Database\Model\TableReference;

/**
 * @author SplitIce
 * A paginated data set
 */
class Paginator implements \IteratorAggregate {
	private $table;
	private $page;
	private $perPage;
	private $set;
	private $totalRows;
	
	public $url;
	public $sql;
	
	/**
	 * Internal method to get a result set
	 */
	private function _get(){
		$sql = clone $this->sql;
		$sql->limit(($this->page-1)*$this->perPage, $this->perPage);
		
		return $this->table->getAll($sql);
	}
	function __construct(TableReference $table,$page = 1, $perPage = 30){
		$this->table = $table;
		$this->page = $page;
		$this->perPage = $perPage;
		$this->set = $this->_get();
		$this->totalRows = $this->table->getAll()->getCount();
		$this->sql = new SQL\SelectStatement();
	}
	
	public function getIterator() {
		$o = $this->set;
		while($o instanceof \IteratorAggregate){
			$o = $o->getIterator();
		}
		return $o;
	}
	
	function OutputLinks(IPaginator $paginator,IPaginationTemplate $template){
		$paginator->Output(ceil($this->totalRows/$this->perPage), $template);
	}
}