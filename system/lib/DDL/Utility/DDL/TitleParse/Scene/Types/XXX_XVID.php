<?php
namespace Utility\DDL\TitleParse\Scene\Types;

class XXX_XVID extends Internal\XXXBase {
	function parse(){
		if(!$this->parts){
			return;
		}
		parent::Parse();
		
		//Check encoding
		$ue = strtoupper($this->encoding);
		if($ue != 'XVID' && $ue != 'DIVX'){
			$this->isValid(false);
		}
	}
	
	function titleBuild(){
		$ret = $this->title;
		if($this->year){
			$ret .= ' ('.$this->year.')';
		}
		if($this->source){
			$ret .= ' '.$this->source;
		}
		return $ret;
	}
}