<?php
namespace Web\PageHandler;

abstract class HTMLPageBase extends PageBase {
	function Title($part = null){
		return $part;
	}
	private $_meta;
	function Meta($what = null){
		if(!$this->_meta){
			$this->_meta = new MetaManager();
		}
		if($what === null) return $this->_meta;
		return $this->_meta[$what];
	}
}