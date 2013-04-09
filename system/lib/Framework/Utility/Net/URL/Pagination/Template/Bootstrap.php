<?php
namespace Utility\Net\URL\Pagination\Template;

use Utility\Net\URL\Pagination\IPaginator;

class Bootstrap extends \Core\Object implements IPaginationTemplate {
	function onePage(){
		return '1 of 1';
	}
	function prevLink(IPaginator $paginator,$page){
		return '<li><a href="'.$paginator->toURL($page).'">&laquo; Prev</a></li>';
	}
	function nextLink(IPaginator $paginator,$page){
		return '<li><a href="'.$paginator->toURL($page).'">Next &raquo;</a></li>';
	}
	function lastLink(IPaginator $paginator,$page){
		return '';
	}
	function pageLink(IPaginator $paginator,$page,$isCurrent=false){
		$url = '';
		$url = $paginator->toURL($page);
		$ret = '';
		$ret .= '<li'.($isCurrent?' class="active"':'').'><a href="'.$url.'">'.$page.'</a></li>';
		return $ret;
	}
}