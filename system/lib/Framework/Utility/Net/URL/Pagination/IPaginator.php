<?php
namespace Utility\Net\URL\Pagination;
use Utility\Net\URL\Pagination\Template\IPaginationTemplate;

interface IPaginator {
	function toURL($page = 1);
	function output($last,IPaginationTemplate $template);
}