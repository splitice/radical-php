<?php
namespace Web\Sitemap\Maps;
use Database\Model\TableReferenceInstance;
use Web\Sitemap\Internal\SitemapContainer;
use Web\Sitemap\Internal\Url;

class Database extends Internal\MapBase {
	/**
	 * @var Database\Model\TableReferenceInstance
	 */
	private $table;
	
	function __construct(TableReferenceInstance $table, $page_number){
		$this->table = $table;
		parent::__construct($page_number);
	}
	protected static function numRows(){
		$class = $this->table->getClass();
		return ceil($class::getAll()->getCount()/static::LINK_LIMIT);
	}
	function getRows(){
		$ret = array();
		
		$sql = 'LIMIT '.($this->page_number*static::LINK_LIMIT).','.static::LINK_LIMIT;
		$class = $this->table->getClass();
		
		foreach($class::getAll($sql) as $object){
			$u = new Url(_U($object));
			//TODO: Interfaces for extra configuration.... maybe sub class system
			//$u->setLastModified($key->getLastDownload());
			//$u->setPriority(self::PRIORITY);
			$ret[] = $u;
		}
		return $ret;
	}
}
