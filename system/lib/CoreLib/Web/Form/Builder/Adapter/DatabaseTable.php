<?php
namespace Web\Form\Builder\Adapter;

use Web\Form\Element;
use Model\Database\Model\Table;
use Web\Form\Builder\Internal;
use Model\Database\Model\TableReferenceInstance;
use Web\Form\FormContainer;

class DatabaseTable implements IAdapter {
	protected $table;
	
	function __construct(TableReferenceInstance $table){
		$this->table = $table;
	}
	
	function getElements(){
		$row = $form = array();
		
		$tableManagement = $this->table->getTableManagement();
		foreach($tableManagement->getColumns() as $colName => $col){
			$row[$colName] = $col->getFormElement();
		}

		return $row;
	}
	
	function getObjectElements(Table $object = null, $row = null){
		if(!$row){
			$row = $this->getElements();
		}
		
		$id = null;
		if($object){
			$id = $object->getId();
			if(!is_array($id)) $id = array($id);
		}
		
		
		$idStr = '';
		if($id){
			foreach($id as $i){
				$idStr .= '['.$i.']';
			}
		}
		
		$formRow = array();
		foreach($row as $colname=>$r){
			$name = $r->attributes['name'];

			$r = clone $r;
			if($object){
				$r->setValue($object->getSQLField($name));
			}
		
			$r->attributes['name'] = $name.$idStr;
		
			$formRow[] = new Element\Label($colname, $r);
			$formRow[] = $r;
		}
		
		return $formRow;
	}
	
	function getAll($sql = null){
		$row = $this->getElements();
		
		$class = $this->table->getClass();
		foreach($class::getAll($sql) as $object){
			$formRow = $this->getObjectElements($object,$row);
			$form[] = new Internal\FormRow($formRow);
		}
		return $form;
	}
	
	function fromId($id){
		$class = $this->table->getClass();
		if($id == null){
			$object = null;
		}else{
			$object = $class::fromId($id);
		}
		$form = $this->getObjectElements($object);
		return $form;
	}
	
	static function is($obj){
		if($obj instanceof TableReferenceInstance) return true;
	}
}