<?php
namespace HTML\Form\Builder;

interface IFormControls {
	function textarea($name, $value);
	function textbox($name, $value);
	function text($name, $value);
	function textinput($name, $value);
	function radio($name, $value, $checked = null);
	function radiobox($name, $value, $checked = null);
	function check($name, $value, $checked = null);
	function checkbox($name, $value, $checked = null);
	function select($name, $value = array());
	function selectbox($name, $value = array());
	function submit($text = 'Submit');
	function reset($text = 'Reset');
	function button($text, $type = 'button');
}