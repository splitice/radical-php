<?php
namespace Model\Database\Model\Pagination;

use Utility\Net\URL\Pagination\IPaginator;
use Utility\Net\URL\Pagination\Template\IPaginationTemplate;

interface IDatabasePaginator extends \IteratorAggregate {
	function OutputLinks(IPaginator $paginator,IPaginationTemplate $template);
}