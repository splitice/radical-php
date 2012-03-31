<?php
namespace Web\Sitemap\Internal;
use Web\Sitemap\SitemapBase;

class Url extends SitemapBase {
	private $url;
	private $priority;
	private $last_modified;
	private $changefreq;
	
	function __construct($url){
		$this->url = $url;
	}
	
	/**
	 * @return the $url
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @return the $priority
	 */
	public function getPriority() {
		return $this->priority;
	}

	/**
	 * @return the $last_modified
	 */
	public function getLastModified() {
		return $this->last_modified;
	}

	/**
	 * @return the $changefreq
	 */
	public function getChangeFreq() {
		return $this->changefreq;
	}

	/**
	 * @param field_type $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * @param field_type $priority
	 */
	public function setPriority($priority) {
		$this->priority = $priority;
	}

	/**
	 * @param field_type $last_modified
	 */
	public function setLastModified($last_modified) {
		$this->last_modified = $last_modified;
	}

	/**
	 * @param field_type $changefreq
	 */
	public function setChangeFreq($changefreq) {
		$this->changefreq = $changefreq;
	}

	function toXML(){
		$ret = '<url><loc>'.$this->url.'</loc>';
		if($this->priority){
			$ret .= '<priority>'.$this->priority.'</priority>';
		}
		if($this->last_modified){
			$ret .= '<lastmod>'.$this->last_modified.'</lastmod>';
		}
		if($this->changefreq){
			$ret .= '<changefreq>'.$this->changefreq.'</changefreq>';
		}
		$ret .= '</url>';
		
		return $ret;
	}
}