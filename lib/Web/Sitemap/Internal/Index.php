<?php
namespace Web\Sitemap\Internal;
use Web\Sitemap\SitemapBase;

class Index extends SitemapBase {
	private $sitemaps = array();
	
	function Add(Sitemap $sitemap){
		$this->sitemaps[] = $sitemap;
	}
	
	function toXML(){
		$ret = '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		foreach($this->sitemaps as $u){
			$ret .= $u->toXML();
		}
		$ret .= '</sitemapindex>';
		
		return $ret;
	}
}