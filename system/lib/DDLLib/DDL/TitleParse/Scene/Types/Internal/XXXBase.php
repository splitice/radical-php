<?php
namespace DDL\TitleParse\Scene\Types\Internal;

class XXXBase extends MovieBase {
	const NATIVE_TYPE = 'xxx';
	
	function isSource($s,$prev){
		if(strtoupper($prev) == 'XXX'){
			if(is_numeric($s) && $s>1600 && $s<(date('Y')+1)){
				$this->year = (int)$s;
				return true;
			}
			return parent::isSource($s);
		}
		return false;
	}
	function isScene($s){
		switch(strtolower($s)){
			case 'hdtv':
			case 'pdtv':
			case 'hr'://FLV high resolution
				return true;
				break;
		}
		return parent::isScene($s);
	}
	
	function Parse() {
		$this->encoding = $this->extractPart ( - 1 );
		do{
			$next = $this->extractPart ( - 1 );
			$n2 = $this->extractPart ( - 1, true );
			if($this->isSource($next,$n2)){
				$this->source = $next;
			}
		}while($this->isScene($next) || $this->isSource($next,$n2));
		
		if($this instanceof \DDL\TitleParse\Scene\Types\XXX_0DAY){
			$this->parts[] = $next;
		}
		
		foreach($this->parts as $pk=>$p){
			if(is_numeric($p) && $p>1000 && $p <= date('Y')){
				$this->parts = array_slice($this->parts, 0, $pk);
				$this->year = $p;
				break;
			}
		}
		
		$this->title = self::CleanString($this->parts);
		$this->parts = array();
	}
}