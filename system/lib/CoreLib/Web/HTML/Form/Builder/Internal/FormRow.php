<?php
namespace HTML\Form\Builder\Internal;

use Basic\ArrayLib\Object\CollectionObject;

class FormRow extends CollectionObject {
	function toHTML(){
		$ret = '<div class="row">';
		foreach($this->data as $r){
			$ret .= $r->toHTML();
		}
		return $ret.'</div>';
	}
}