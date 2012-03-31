<?php
namespace Web\Sitemap\Internal;
use Web\Sitemap\SitemapBase;

class Urlset extends SitemapBase {
	private $urls = array();
	
	function Add(Url $url){
		$this->urls[] = $url;
	}
	
	function toXML(){
		$ret = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		foreach($this->urls as $u){
			$ret .= $u->toXML();
		}
		$ret .= '</urlset>';
		
		return $ret;
	}
}