<?php
namespace Database\Model\Pagination;

use Net\URL\Pagination\IPaginator;
use Net\URL\Pagination\Template\IPaginationTemplate;

interface IDatabasePaginator extends \IteratorAggregate {
	function OutputLinks(IPaginator $paginator,IPaginationTemplate $template);
}