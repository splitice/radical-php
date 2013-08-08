<?php
namespace Web\Form\Element;

use Utility\HTML\Element;

class Label extends Element {
	protected $name;
	protected $element;
	
	function __construct($name,Internal\FormElementBase $element){
		$this->element = $element;
		
		if($element instanceof CheckBox){
			$element->html_override('');
		}
		
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
	
	function __toString(){
		if($this->element instanceof CheckBox){
			$this->element->html_override(null);

			$inner = $this->inner;
			$this->inner .= (string)$this->element;

			$this->element->html_override('');
			
			$ret = parent::__toString();
			
			$this->inner = $inner;
			
			return $ret;
		}
		
		return parent::__toString();
	}
}