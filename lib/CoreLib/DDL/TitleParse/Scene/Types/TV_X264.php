<?php
namespace DDL\TitleParse\Scene\Types;

class TV_X264 extends Internal\TVBase {
	protected $quality;
	
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
		parent::Parse();
		if(strtolower($this->encoding) != 'x264'){
			$this->isValid(false);
		}
	}
	/**
	 * @return the $quality
	 */
	public function getQuality() {
		return $this->quality;
	}

	
}