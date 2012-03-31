<?php
namespace DDL\TitleParse\Scene\Types;

class DVDR extends Internal\MovieBase {
	function sourceValidate(){
		return true;
	}
	function Parse(){
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