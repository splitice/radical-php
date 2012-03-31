<?php
namespace HTML\Tag;

class Script extends \HTML\Element {
	function __construct($src = null,$type='text/javascript'){
		parent::__construct('script',array('src'=>$src,'type'=>$type));
	}
}