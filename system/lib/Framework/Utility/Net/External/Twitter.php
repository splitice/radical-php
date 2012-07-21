<?php
namespace Utility\Net\External;

class Twitter {
	protected $id;
	
	function __construct($id){
		$this->id = $id;
	}
	
	protected function getUrl(){
		return 'https://twitter.com/statuses/user_timeline/'.$this->id.'.xml?count=1';
	}
	
	function getStatus(){
		$data = file_get_contents($this->getUrl());
		
		if(preg_match('#<text>([^<]+)</text>#',$data,$m)){
			return $m[1];
		}
		
		return null;
	}
}