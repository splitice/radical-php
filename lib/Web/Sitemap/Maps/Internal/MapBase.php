<?php
namespace Web\Sitemap\Maps\Internal;
use Web\Sitemap\Internal;
use Web\Sitemap\SitemapBase;

abstract class MapBase extends SitemapBase {
	protected $page_number = 0;
	
	function __construct($page_number){
		$this->page_number = $page_number;
	}
	static function toURL($data){
		return 'sitemap.'.static::KEY.'.'.$data.'.xml';
	}
	function getPageNumber(){
		return $this->page_number;
	}
	static function instances(){
		$c = get_called_class();
		$ret = array();
		for($i=0,$f=$c::numRows();$i<$f;++$i){
			$obj = new $c($i);
			$ret[] = $obj;
		}
		return $ret;
	}
	static function keyName(){
		return static::KEY;
	}
	static function isKey($k){
		return ($k==static::KEY);
	}
	static function U($u){
		return _U($u);
	}
	function toXML(){
		$ret = new Internal\Urlset();
		foreach($this->getRows() as $r){
			$ret->Add($r);
		}
		return $ret->toXML();
	}
}
