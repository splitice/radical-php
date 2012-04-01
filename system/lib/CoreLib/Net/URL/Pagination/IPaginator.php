<?php
namespace Net\URL\Pagination;
use Net\URL\Pagination\Template\IPaginationTemplate;

interface IPaginator {
	function toURL($page = 1);
	function Output($last,IPaginationTemplate $template);
}