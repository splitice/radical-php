<?php
namespace HTML\Form\Element;

class SelectBox extends Internal\FormElementBase {
	public function setValue($value) {
		if(is_array($value)){
			$this->inner = $value;
		}else{
			foreach($this->inner as $v){
				if($v->getValue() == $value){
					foreach($this->inner as $vc){
						$vc->setSelected(false);
					}
					$v->setSelected(true);
					break;
				}
			}
		}
	}
	function __construct($name,$options = array()){
		parent::__construct('select',$name);
		$this->inner = $options;
	}
}
