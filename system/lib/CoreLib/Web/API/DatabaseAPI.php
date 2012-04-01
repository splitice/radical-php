<?php
namespace Web\API;

use Database\Model\TableReferenceInstance;

abstract class DatabaseAPI extends APIBase {
	var $class;
	protected $table;
	protected $api;
	
	function __construct($data,$type){
		parent::__construct($data,$type);
		$this->table = new TableReferenceInstance($this->class);
		$this->api = new \Database\API($this->table);
	}
	abstract function Validate($method,$data);
	abstract function ValidateSelect($ret);
	
	function Insert(){
		$data = $this->data['data'];
		if($this->Validate('insert',$data)){
			return $this->api->Insert($data);
		}
		$this->ValidationException('Insert not allowed due to unauthorised elements in set or in changes');
	}
	function Update(){
		$objs = $this->api->Select($this->data['where']);
		if($this->Validate('update',array('where'=>$this->data['where'],'on'=>$objs,'set'=>$this->data['set']))){
			return $this->api->Update($objs,$this->data['set']);
		}
		$this->ValidationException('Update not allowed due to unauthorised elements in set or in changes');
	}
	function Delete(){
		$objs = $this->api->Select($this->data['where']);
		if($this->Validate('delete',array('on'=>$objs))){
			return $this->api->Delete($objs);
		}
		$this->ValidationException('Delete not allowed due to unauthorised elements in set');
	}
	function Select(){
		if($this->Validate('select',array('where'=>$this->data['where']))){
			$objs = $this->api->Select($this->data['where']);
			if($this->ValidateSelect($objs)){
				$ret = array();
				foreach($objs as $o){
					$ret[] = array('data'=>$o,'id'=>$o->getIdentifyingSQL());
				}
				return $ret;
			}
			$this->ValidationException('Select not allowed due to unauthorised elements in set');
		}
		$this->ValidationException('Select not allowed due to unauthorised query (WHERE)');
	}
	private function ValidationException($message){
		throw new \Exception($message);
	}
}