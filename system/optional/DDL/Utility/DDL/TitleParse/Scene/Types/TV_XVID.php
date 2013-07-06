<?php
namespace Utility\DDL\TitleParse\Scene\Types;

class TV_XVID extends Internal\TVBase {
	function parse(){
		if(!$this->parts){
			return;
		}
		parent::Parse();
		if(strtoupper($this->encoding) != 'XVID'){
			$this->isValid(false);
		}
	}
}