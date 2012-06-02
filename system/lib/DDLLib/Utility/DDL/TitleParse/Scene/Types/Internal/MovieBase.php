<?php
namespace DDL\TitleParse\Scene\Types\Internal;

class MovieBase extends SceneBase {
	protected $title;
	protected $year;
	protected $encoding;
	protected $source;
	
	const NATIVE_TYPE = 'movie';
	
	function __construct($string) {
		parent::__construct ( $string );
		$this->Parse ();
	}
	
	static function CleanString($str) {
		$str = implode ( ' ', $str );
		return trim ( $str );
	}
	
	function isScene($s){
		$lower = strtolower($s);
		switch($lower){
			case 'ac3':
			case 'line':
			case 'limited':
			case 'unrated':
			case 'propper':
			case 'extras':
			case 'subpack':
			case 'subpack':
			case 'v2':
			case 'v3':
			case 'hq':
			case 'lq':
			case '2cd':
			case '3cd':
			case 'resync':
			case 'limited':
			case 'complete':
				return true;
				break;
			case 'ntsc':
			case 'pal':
				if($this->source == null){
					$this->source = $s;
				}
				return true;
		}
		if(substr($lower,4) == 'ac3-'){//ac3-5.1 etc
			if(is_float(substr($lower,3))){
				return true;
			}
		}
		return false;
	}
	function sourceValidate(){
		return $this->source;
	}
	function isEncoding($s){
		switch(strtolower($s)){
			case 'xvid':
			case 'divx':
				return true;
				break;
		}
		return false;
	}
	function isSource($s){
		switch(strtolower($s)){
			case 'dvdrip':
			case 'bdrip':
			case 'bluray':
			case 'bd':
			case 'dvdr':
			case 'dvd5':
			case 'dvd9':
			case 'cam':
			case 'ts':
			case 'tc':
			case 'dvdscr':
			case 'scr':
				return true;
				break;
		}
		if(preg_match('#rc?([0-9])#i',$s)){
			return true;
		}
		return false;
	}
	
	function Parse() {
		if(!$this->parts){
			return;
		}
		do{
			$next = $this->extractPart ( - 1 );
			
			if($this->isEncoding($next)){
				$this->encoding = $next;
			}
			
			if($next == 'XXX'){
				$this->isValid(false);
				$this->parts = array();
				return;
			}
			
			if($this->isSource($next)){
				$this->source = $next;
				if(strtoupper($this->extractPart ( - 1, true )) == 'XXX'){
					$this->setValid(false);
					$this->parts = array();
					return;
				}
			}
		}while($this->isScene($next) || $this->isSource($next) || $this->isEncoding($next));

		if(!$this->sourceValidate()){
			$this->isValid(false);
			$this->parts = array();
			return;
		}

		if(is_numeric($next) && $next > 1000){
			$this->year = $next;
		}else{
			foreach($this->parts as $pk=>$p){
				if(self::isYear($p)){
					$this->parts = array_slice($this->parts, 0, $pk);
					$this->year = $p;
					break;
				}
			}
			if(!$this->year){
				$this->parts[] = $next;
			}
		}
		$this->title = self::CleanString($this->parts);
		$this->parts = array();
	}
	
	static function isYear($p){
		if(is_numeric($p)){
			$p = (int)$p;
			return ($p>1000 && ($p-1) <= @date('Y'));
		}
		return false;
	}
	
	function BackupParse(){
		$this->valid = false;
		foreach($this->parts as $pk=>$p){
			if(static::isYear($p)){
				$this->year = $p;
				$this->title = $this->CleanString(array_slice($this->parts, 0, $pk));
				$this->parts = array_values(array_slice($this->parts,$pk+1));
				$this->valid = true;
				break;
			}
		}
		
		do{
			$next = $this->extractPart ( - 1 );
		
			if($this->isEncoding($next)){
				$this->encoding = $next;
			}
		
			if($next == 'XXX'){
				$this->isValid(false);
				$this->parts = array();
				return;
			}
		
			if($this->isSource($next)){
				$this->source = $next;
				if(strtoupper($this->extractPart ( - 1, true )) == 'XXX'){
					$this->setValid(false);
					$this->parts = array();
					return;
				}
			}
		}while($this->parts);
	}
	
	function TitleBuild(){
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