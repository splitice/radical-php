<?php
namespace Net\ExternalInterfaces\ContentAPI;

class Remote {
	protected $server;
	protected $module;
	
	function __construct($server,$module){
		$this->server = $server;
		$this->module = $module;
	}
	function Fetch($id){
		$query = array('module'=>$this->module,'id'=>$id);
		
		$api = new \Web\APICall($this->server);
		$data = $api->Call('External', 'getById', $query,'ps');
		
		return $data;
	}
}