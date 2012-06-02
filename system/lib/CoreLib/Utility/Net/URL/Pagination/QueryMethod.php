<?php
namespace Net\URL\Pagination;

class QueryMethod extends Internal\PaginationBase implements IPaginator {
	protected $query;
	
	function __construct($url = null,$query = 'page'){
		//Create URL object
		if($url == null){
			$url = \Net\URL::fromRequest();
		}else{
			$url = \Net\URL::fromURL($url);
		}
		
		//Reset Page
		$query_string = $url->getPath()->getQuery();
		if(isset($query_string[$query])){
			$this->current = (int)$query_string[$query];
			unset($query_string[$query]);
			$url->getPath()->setQuery($query_string);
		}else{
			$this->current = 1;
		}
		
		$this->query = $query;
		parent::__construct($url);
	}
	function toURL($page = 1){
		if($page<=1){
			return $this->url;
		}else{
			$url = clone $this->url;
			
			//Query String
			$query = $url->getPath()->getQuery();
			$query[$this->query] = $page;
			$url->getPath()->setQuery($query);
			
			//Return url
			return (string)$url;
		}
	}
}