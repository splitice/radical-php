<?php
namespace Net\URL\Pagination\Template;

use Net\URL\Pagination\IPaginator;

class Standard extends \Core\Object implements IPaginationTemplate {
	function onePage(){
		return '1 of 1';
	}
	function prevLink(IPaginator $paginator,$page){
		return '<a href="'.$paginator->toURL($page).'">&laquo; Prev</a> | ';
	}
	function nextLink(IPaginator $paginator,$page){
		return '| <a href="'.$paginator->toURL($page).'">Next &raquo;</a>';
	}
	function lastLink(IPaginator $paginator,$page){
		return ' ... <a href="'.$paginator->toURL($page).'">'.$page.'</a>';
	}
	private $firstPage = null;
	function pageLink(IPaginator $paginator,$page,$isCurrent=false){
		$url = '';
		$url = $paginator->toURL($page);
		if($this->firstPage == null){
			$this->firstPage = $page;
			$ret = '<a href="'.$paginator->toURL(1).'">1</a> ... ';
		}
		if($page != 1){
			$ret .= (($isCurrent?'<span class="selected">':('<a href="'.$url.'">')).$page.($isCurrent?'</span>':'</a>').' ');
		}
		return $ret;
	}
}