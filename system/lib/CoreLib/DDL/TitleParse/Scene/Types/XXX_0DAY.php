<?php
namespace DDL\TitleParse\Scene\Types;

class XXX_0DAY extends XXX_X264 {
	static $exts = array('avi','mkv','wmv','mov','mp4','m4v','flv','f4v');
	protected $site;
	protected $date;
	
	/**
	 * @return the $site
	 */
	public function getSite() {
		return $this->site;
	}

	/**
	 * @return the $date
	 */
	public function getDate() {
		return $this->date;
	}

	function isImageset(){
		if(strtoupper($this->encoding) == 'IMAGESET'){
			return true;
		}
		return false;
	}
	function isSource($s1,$s){
		if(strtoupper($s1) == 'XXX'){
			return true;
		}
		return false;
	}
	function encodingValidate($v){
		if(in_array(strtolower($v), static::$exts)){
			return true;
		}
		return $this->isImageset();
	}
	
	function ParseDate($ref_pos) {
		$date = array();
		$date[] = $this->extractPart($ref_pos);
		$date[] = $this->extractPart($ref_pos);
		$date[] = $this->extractPart($ref_pos);
		$date = array_reverse($date);//it is always reversed
		if(strlen($date[2]) == 2){
			$date[2] = '20'.$date[2];
		}
		$this->date =  implode('.',$date);
	}
	function FillSD($i){
		$this->site = implode('.',array_slice($this->parts,0,$i));
		$this->title = implode(' ',array_slice($this->parts,$i,100));
	}
	function Parse(){
		if(!$this->parts){
			return;
		}
		$this->_x264 = false;
		parent::Parse();
		if(!$this->encodingValidate($this->encoding)){
			$this->isValid(false);
			return;
		}
		if($this->valid){
			$this->parts = explode(' ',$this->title);

			//Look for date
			for($i=0,$f=count($this->parts)-2;$i<$f;++$i){
				if(is_numeric($this->parts[$i]) && is_numeric($this->parts[$i+1]) && is_numeric($this->parts[$i+2])){
					$this->ParseDate($i);
					break;
				}
			}
			
			if($this->date){
				$this->FillSD($i);
			}else{
				//Cant find date
				//Look for E{n+}
				$found = false;
				foreach($this->parts as $pk=>$p){
					if(preg_match('#^E([0-9]+)$#s',$p)){
						$found = true;
						$this->episode = (int)substr($this->extractPart($pk),1);
						$this->FillSD($pk);
					}
				}
				
				if(!$found){
					//Not found E ref, persume whole thing is the title
					$this->FillSD(1);
				}
			}
			
			$this->parts = array();
		}
	}
	
	function TitleBuild($include_date=false){
		$ret = '';
		if($this->isImageset()){
			$ret .= '[iMAGESET] ';
		}
		if($this->site){
			//Upper = new word
			if(strpos($this->site,'.')){
				$site = $this->site;
			}else{
				$site = $this->site{0};
				for($i=1,$f=strlen($this->site);$i<$f;$i++){
					if(isset($this->site{$i+1}) && ctype_upper($this->site{$i}) && ctype_lower($this->site{$i+1})){
						$site .= ' ';
					}
					$site .= $this->site{$i};
				}
			}
			
			//Build String
			$ret .= $site.' - ';
			if($this->date && $include_date){
				$ret .= $this->date .= ' - ';
			}
			$ret .= $this->title;
		}else{
			$ret .= $this->title;
			if($this->date && $include_date){
				$ret .=  ' - '.$this->date;
			}
		}
		return $ret;
	}
}
