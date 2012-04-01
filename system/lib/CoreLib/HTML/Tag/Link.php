<?php
namespace HTML\Tag;

class Link extends \HTML\Element {
	function __construct($rel,$href,$media=null){
		parent::__construct('link',array('rel'=>$rel,'href'=>$href,'media'=>$media));
	}
}