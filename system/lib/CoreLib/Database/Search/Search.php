<?php
namespace Database\Search;

class Search {
	protected $adapter;
	protected $text;
	
	function __construct($text,$adapter){
		$this->text = $text;
		$this->adapter = $adapter;
	}
	
	function Execute(){
		return $this->adapter->Search($this->text);
	}
}