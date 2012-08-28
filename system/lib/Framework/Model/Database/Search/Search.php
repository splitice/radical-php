<?php
namespace Model\Database\Search;

class Search {
	protected $adapter;
	protected $text;
	
	function __construct($text,$adapter){
		$this->text = $text;
		$this->adapter = $adapter;
	}
	
	function execute(){
		return $this->adapter->Search($this->text);
	}
}