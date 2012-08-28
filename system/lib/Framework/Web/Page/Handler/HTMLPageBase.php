<?php
namespace Web\Page\Handler;

abstract class HTMLPageBase extends PageBase {
	function title($part = null){
		return $part;
	}
	private $_meta;
	function meta($what = null){
		if(!$this->_meta){
			$this->_meta = new MetaManager();
		}
		if($what === null) return $this->_meta;
		return $this->_meta[$what];
	}
}