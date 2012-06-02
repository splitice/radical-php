<?php
namespace HTML\Tag;

class Meta extends \HTML\Element {
	function __construct($name,$content){
		parent::__construct('meta',array('name'=>$name,'content'=>$content));
	}
}