<?php
namespace Model\Database\Model;

use Model\Database\SQL\Parts\Expression\In;
use Model\Database\SQL\Parts\Expression\Comparison;
class DeleteBuffer {
	private $table;
	private $data = array();
	
	function __construct(TableReferenceInstance $table){
		$this->table = $table;
	}
	
	function add(Table $table){
		$this->data[] = $table->getId();
	}
	
	function delete(){
		if($this->data){
			$id = $this->table->getORM()->id;
			if(count($id) == 1){
				$id = $id[0];
			}else{
				die('Not supported');
			}
			
			$sql = $this->table->delete(new Comparison($id, new In($this->data), null));
			$ret = $sql->query();
			$this->data = array();
			return $ret;
		}
	}
}