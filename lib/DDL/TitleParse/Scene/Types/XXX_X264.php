<?php
namespace DDL\TitleParse\Scene\Types;

class XXX_X264 extends Internal\XXXBase {
	protected $quality;
	protected $_x264 = true;
	
	function Parse(){
		if(!$this->parts){
			return;
		}
		for($i=count($this->parts)-1;$i>=0;--$i){
			if(is_numeric(substr($this->parts[$i],0,-1))){
				$l = strtolower(substr($this->parts[$i],-1));
				if($l=='p' || $l=='i'){
					$this->quality = $this->parts[$i];
					unset($this->parts[$i]);
					break;
				}
			}
		}
		$this->parts = array_values($this->parts);
		parent::Parse();
		if(strtolower($this->encoding) != 'x264' && $this->_x264){
			$this->isValid(false);
		}
	}
	/**
	 * @return the $quality
	 */
	public function getQuality() {
		return $this->quality;
	}

	function TitleBuild(){
		$ret = $this->title;
		if($this->year){
			$ret .= ' ('.$this->year.')';
		}
		if($this->quality){
			$ret .= ' '.$this->quality;
		}
		if($this->source){
			$ret .= ' '.$this->source;
		}
		return $ret;
	}
}