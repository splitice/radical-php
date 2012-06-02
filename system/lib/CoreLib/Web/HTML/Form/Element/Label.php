<?php
namespace HTML\Form\Element;

use HTML\Element;

class Label extends Element {
	protected $name;
	protected $element;
	
	function __construct($name,Internal\FormElementBase $element){
		$this->element = $element;
		
		//set for
		$attributes = array(); 
		if(isset($element->attributes['id'])){
			$attributes['for'] = &$element->attributes['id'];
		}else{
			$id = md5($name);
			$element->attributes['id'] = $id;
			$attributes['for'] = &$element->attributes['id']; 
		}
		
		parent::__construct('label',$attributes,$name);
	}
}