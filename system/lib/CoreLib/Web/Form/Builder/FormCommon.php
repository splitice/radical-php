<?php
namespace Web\Form\Builder;
use Web\Form\Element;

class FormCommon implements IFormControls {
	protected function _R($return){
		return $return;
	}
	
	function begin_form($action,$method = 'POST'){
		return new Element\Form($action,$method);
	}
	function end_form(){
		return '</form>';
	}
	
	function label($name, Element\Internal\FormElementBase $element,$before = true){
		if($before) $before = $element;
		return $this->_R(new Element\Label($name, $element),$before);
	}
	
	function textarea($name, $value){
		return $this->textbox($name, $value);
	}
	function textbox($name, $value){
		return $this->_R(new Element\TextArea($name, $value));
	}
	
	function hidden($name, $value = ''){
		return $this->_R(new Element\HiddenInput($name, $value));
	}
	
	function number($name, $value = ''){
		return $this->_R(new Element\TextInput($name, $value ,'number'));
	}
	function email($name, $value = ''){
		return $this->_R(new Element\TextInput($name, $value ,'email'));
	}
	function text($name, $value = ''){
		return $this->textinput($name, $value);
	}
	function textinput($name, $value = ''){
		return $this->_R(new Element\TextInput($name, $value));
	}
	
	function password($name, $value = ''){
		return $this->_R(new Element\TextInput($name, $value ,'password'));
	}
	
	function radio($name, $value, $checked = null){
		return $this->radiobox($name, $value, $checked);
	}
	function radiobox($name, $value, $checked = null){
		return $this->_R(new Element\RadioBox($name, $value, $checked));
	}
	
	function check($name, $value, $checked = null){
		return $this->checkbox($name, $value, $checked);
	}
	function checkbox($name, $value, $checked = null){
		return $this->_R(new Element\CheckBox($name, $value, $checked));
	}
	
	function select($name, $value = array()){
		return $this->selectbox($name, $value);
	}
	function selectbox($name, $value = array()){
		return $this->_R(new Element\SelectBox($name, $value));
	}
	
	function submit($text = 'Submit'){
		return $this->_R(new Element\SubmitButton($text));
	}
	function reset($text = 'Reset'){
		return $this->_R(new Element\ResetButton($text));
	}
	function button($text, $type = 'button'){
		switch($type){
			case 'submit':
				return $this->submit($text);
	
			case 'reset':
				return $this->reset($text);
	
			case 'button':
				return $this->_R(new Element\Button($text));
		}
	}
}