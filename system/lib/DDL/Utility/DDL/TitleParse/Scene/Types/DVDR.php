<?php
namespace Utility\DDL\TitleParse\Scene\Types;

class DVDR extends Internal\MovieBase {
	function sourceValidate(){
		return true;
	}
	function parse(){
		if(!$this->parts){
			return;
		}
		parent::Parse();
		if(strtoupper($this->encoding) != 'DVDR'){
			$this->parts = array();
			$this->isValid(false);
		}
	}
}