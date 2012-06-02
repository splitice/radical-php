<?php
namespace Utility\HTML\Tag;

use Utility\HTML\Element;

class Meta extends Element {
	function __construct($name,$content){
		parent::__construct('meta',array('name'=>$name,'content'=>$content));
	}
}