<?php
namespace DDL\TitleParse\Scene\Types;

class TV_Anime extends Internal\BasicBase {
	protected $quality;
	protected $group;
	protected $encoding;
	protected $title;
	protected $title_sub;
	protected $episode;
	protected $episode_title;
	
	const NATIVE_TYPE = 'tv';
	const DELIMITER = ' ';
	
	static $_qualities = array(240,480,720,1080);
	
	function Split($str){
		$ret = array();
		$active = '';
		$in_bracket = false;//false,']' or ')'
		for($i=0,$f=strlen($str);$i<$f;++$i){
			$split = false;
			if($in_bracket){
				if($str{$i} == $in_bracket){
					$split = true;
					$in_bracket = false;
				}
			}elseif($str{$i} == ' '){
				$split = true;
			}elseif($str{$i} == '['){
				$in_bracket = ']';
			}elseif($str{$i} == '('){
				$in_bracket = ')';
			}
			$active .= $str{$i};
			if($split && trim($active)){
				$ret[] = trim($active);
				$active = '';
			}
		}
		return $ret;
	}
	
	function parseQuality($str){//encoding, quality
		$ret = false;
		if(preg_match('#('.implode('|',static::$_qualities).')p#s',$str,$m)){
			$this->quality = $m[0];
			$ret = true;
		}else if(preg_match('#([0-9]+)x('.implode('|',static::$_qualities).')#s',$str,$m)){
			$this->quality = $m[2].'p';
			$ret = true;
		}else{
			$e = explode(',',$str);
			if(count($e)>1){
				foreach($e as $ee){
					if(in_array(trim($ee),static::$_qualities)){
						$this->quality = trim($ee).'p';
					}
				}
			}
		}
		if(preg_match('#(xvid|divx)#is',$str)){
			$this->encoding = 'xvid';
			$ret = true;
		}elseif(stripos($str,'x264') !== false || preg_match('#(h\.?264)#is',$str)){
			$this->encoding = 'x264';
			$ret = true;
		}
		
		return $ret;
	}
	function Parse(){
		if(!$this->parts){
			return;
		}
		
		//Parse Group
		$group = $this->extractPart(0,true);
		if(preg_match('#^\[([^\]]+)\]$#',$group,$m)){
			$this->group = $m[1];
			if($this->parseQuality($this->group)){//If this is quality something is wrong
				$this->isValid(false);
				return;
			}else{
				$this->extractPart();
			}
		}
		
		//Remove Filename if exists
		$last = $this->extractPart(-1,true);
		if(false !== strrpos($last,'.')){
			$ext = strtolower(pathinfo($last,PATHINFO_EXTENSION));
			if(in_array($ext, XXX_0DAY::$exts)){
				$last = substr($last,0,-1*(strlen($ext)+1));
				$last = trim($last);
				if($last){
					$this->parts[count($this->parts)-1] = $last;
				}else{
					array_pop($this->parts);
				}
			}
		}
		
		//Remove Usenet/Release tag [XXXXXXXX]
		foreach($this->parts as $pk=>$p){
			if(preg_match('#^\[([0-9A-F]{8})\]$#is',$p)){
				unset($this->parts[$pk]);
				$this->parts = array_values($this->parts);
			}
		}
		
		//Look for quality
		foreach($this->parts as $pk=>$p){
			if(preg_match('#^\(|\[([^\]^\)]+)\]|\)$#is',$p,$m)){
				if($this->parseQuality($m[1])){
					unset($this->parts[$pk]);
				}
			}
		}
		
		//Look again more broadly
		foreach($this->parts as $pk=>$p){
			if($this->parseQuality($p)){
				unset($this->parts[$pk]);
			}
		}
		
		$str = static::CleanString($this->parts);
		$this->parts = array();
		
		if(preg_match('#^(.+)\- (.+) Episode ([0-9]+)(v(?:[0-9]))?#s',$str,$m)){
			$this->title = trim($m[1]);
			$this->title_sub = trim($m[2]);
			$this->episode = (int)trim($m[3]);
		}elseif(preg_match('#^([^\(^\[]+)\- ([0-9]+)(v(?:[0-9]))?#s',$str,$m)){
			$this->title = trim($m[1]);
			$this->episode = (int)trim($m[2]);
		}elseif(preg_match('#^([^\(^\[]+) ([0-9]+)(v(?:[0-9]))?#s',$str,$m)){
			$this->title = trim($m[1]);
			$this->episode = (int)trim($m[2]);
		}elseif(preg_match('#^([^\(^\[]+)#s',$str,$m)){
			$this->title = trim($m[1]);
		}else{
			$this->isValid(false);
		}
		
		if(substr($this->title,-4) == ' OVA'){
			$this->episode = 'OVA '.$this->episode;
			$this->title = trim(substr($this->title,0,-4),' -');
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
		if($this->title_sub){
			$ret .= ': '.$this->title_sub;
		}
		if($this->episode){
			$ret .= ' - '.$this->episode;
		}
		if($this->episode_title){
			$ret .= ' - '.$this->episode_title;
		}
		if($this->quality){
			$ret .= ' ('.$this->quality.')';
		}
		return $ret;
	}
	
	/**
	 * @return the $group
	 */
	public function getGroup() {
		return $this->group;
	}

	/**
	 * @return the $encoding
	 */
	public function getEncoding() {
		return $this->encoding;
	}

	/**
	 * @return the $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @return the $title_sub
	 */
	public function getTitleSub() {
		return $this->title_sub;
	}

	/**
	 * @return the $episode
	 */
	public function getEpisode() {
		return $this->episode;
	}

	/**
	 * @return the $episode_title
	 */
	public function getEpisodeTitle() {
		return $this->episode_title;
	}

	static function is($release,$type_sure=false){
		if($type_sure || strpos($release,' ') !== false){
			$c = get_called_class();
			$obj = new $c($string);
			if($obj->isValid()){
				if($this->getEpisode() || $this->getGroup()){//Otherwise it could be anything non standard
					return true;
				}
			}
		}
		return false;
	}
}