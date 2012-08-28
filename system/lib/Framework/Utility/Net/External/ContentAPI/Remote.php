<?php
namespace Utility\Net\External\ContentAPI;

use Web\Page\API;

class Remote {
	protected $server;
	protected $module;
	
	function __construct($server,$module){
		$this->server = $server;
		$this->module = $module;
	}
	function fetch($id){
		$query = array('module'=>$this->module,'id'=>$id);
		
		$api = new API\APICall($this->server);
		$data = $api->Call('External', 'getById', $query,'ps');
		
		return $data;
	}
}