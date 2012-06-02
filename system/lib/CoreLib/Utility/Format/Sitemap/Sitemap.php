<?php
namespace Web\Sitemap\Internal;
use Web\Sitemap\SitemapBase;

class Sitemap extends SitemapBase {
	private $class;
	
	function __construct($obj){
		$this->class = $obj;
	}
	function toXML(){
		$ret = '';
		$ret .= '<sitemap>';
		$ret .= '<loc>http://www.nexusddl.com/sitemap.';
		$ret .= $this->class->keyName().'.'.$this->class->getPageNumber().'.xml</loc>';
		if(method_exists($this->class, 'getLastModified')){
			$ret .= '<lastmod>'.gmdate(DATE_W3C,$this->class->getLastModified()).'</lastmod>';
		}
		$ret .= '</sitemap>';
		return $ret;
	}
}