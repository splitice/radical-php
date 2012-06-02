<?php
namespace Model\Database\Model\Pagination;

use Model\Database\Model\Table\TableSet;
use Model\Database\Model\TableReferenceInstance;
use Net\URL\Pagination\IPaginator;
use Net\URL\Pagination\Template\IPaginationTemplate;
use Model\Database\SQL;
use Model\Database\Model\TableReference;

/**
 * @author SplitIce
 * A paginated data set
 */
class Paginator implements IDatabasePaginator {
	private $source;
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
		
		return $this->source->Filter($sql);
	}
	function __construct($source,$page = 1, $perPage = 30){
		if($source instanceof TableReferenceInstance){
			$source = $source->getAll();
		}elseif(!($source instanceof  TableSet)){
			throw new \Exception('Invalid Source passed to paginator');
		}
		
		$this->source = $source;
		$this->page = $page;
		$this->perPage = $perPage;
		$this->sql = new SQL\SelectStatement();
		$this->set = $this->_get();
		$this->totalRows = $this->source->getCount();
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