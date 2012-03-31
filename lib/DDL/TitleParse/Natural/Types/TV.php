<?php
namespace DDL\TitleParse\Natural\Types;

class TV extends Internal\NaturalBase {
	const IS_NOT = 0;
	const IS_SURE = 1;
	const IS_MAYBE = 2;
	
	const MAX_EPISODE_RANGE = 10;
	
	static $FH_PREFIXES = array('RS','FS','FSV','FSC','MU','HF','DF','DL','UL');
	
	protected $show;
	protected $season;
	protected $episode;
	protected $ref_type;
	protected $is_anime = self::IS_MAYBE;
	
	function __construct($str){
		parent::__construct($str);
		$this->Parse();
	}
	function parseReal($g,$rls){
		$m = array();
		$before = substr($rls,0,stripos($rls,$g[0]));
		$this->show = rtrim(trim($before),' -');
		
		$g[1] = strtoupper($g[1]);
		if($g[1]=='S'){
			$this->season = (int)$g[2];
		}elseif($g[1]=='E'){
			$this->episode = (int)$g[2];
		}
		
		$e = trim($g[3]);
		if(!$e){
			return;
		}
		
		if(preg_match('#^\s?\-\s?'.$g[1].'?([0-9]+)#i',$e,$m)){
			if($g[1]=='S'){
				$this->season = range((int)$this->season,(int)$m[1]);
			}elseif($g[1]=='E'){
				$this->episode = range((int)$this->episode,(int)$m[1]);;
			}
		}
		
		//Repeated multiple of the same (S01S02)
		while(preg_match('#^'.$g[1].'?([0-9]+)#i',$e,$m)){
			if($g[1]=='S'){
				if(!is_array($this->season)){
					$this->season = array($this->season);
				}
				$this->season[] = (int)$m[1];
			}elseif($g[1]=='E'){
				if(!is_array($this->episode)){
					$this->episode = array($this->episode);
				}
				$this->episode[] = (int)$m[1];
			}
			$e = substr($e,strlen($m[0]));
		}
		
		if($g[1]=='S'){	
			//E01E02		
			while(preg_match('#^E([0-9]+)#i',$e,$m)){
				if(!$this->episode){
					$this->episode = array();
				}
				$this->episode[] = (int)$m[1];
				$e = substr($e,strlen($m[0]));
			}
			if(preg_match('#^\-E?([0-9]+)#i',$e,$m)){
				$this->episode = range($this->episode,(int)$m[1]);
				$e = substr($e,strlen($m[0]));
			}
			if(is_array($this->episode) && count($this->episode)==1){
				$this->episode = $this->episode[0];
			}
		}
	}
	function parseReal2($g,$rls){
		$this->show = rtrim(trim($g[1]),' -');
		
		$this->season = (int)$g[2];
		$this->episode = (int)$g[3];
		
		$e = trim($g[4]);
		if(!$e){
			return;
		}
		
		if(strlen($e) >= 2 && $e{0}=='-' && is_numeric($e{1})){
			$num = '';
			$e = substr($e,1);
			while(!empty($e) && is_numeric($e{0})){
				$num .= $e;
				$e = substr($e,1);
			}
			$this->episode = range($this->episode,(int)$num);
		}
	}
	function parseEpNum($g,$rls){
		$m = array();
		$before = substr($rls,0,stripos($rls,$g[0]));
		$this->show = rtrim(trim($before),' -');
		
		$this->season = null;
		$this->episode = (int)$g[1];
		
		$e = trim($g[2]);
		if(!$e){
			return;
		}
		
		if(strlen($e) >= 2 && $e{0}=='-' && is_numeric($e{1})){
			$num = '';
			$e = substr($e,1);
			while(!empty($e) && is_numeric($e{0})){
				$num .= $e;
				$e = substr($e,1);
			}
			if(abs($this->episode-$num)<self::MAX_EPISODE_RANGE){
				$this->episode = range($this->episode,(int)$num);
			}
		}
	}
	function parseNatural($g,$rls){
		preg_match('#^(.+)\s*(\-?)\s*(Season|Series|Episode)\s*([0-9]+)#Ui',$rls,$mm);
		$this->show = $mm[1];
		
		$e = $g[3];
		
		$this->season = $this->episode = array();
		
		$type = strtolower($g[1]);
		if($type=='season'){
			$this->season[] = (int)$g[2];
		}elseif($type=='episode'){
			$this->episode[] = (int)$g[2];
		}
		
		if(!$e){
			return $e;
		}
		
		while(preg_match('#(\-?)\s*((?:Season|Series|Episode)?)\s*([0-9]+)(.*)$#i',$e,$m)){
			if($m[2]){
				$m[2] = strtolower($m[2]);
				if($m[2] == 'series'){
					$m[2] = 'season';
				}
			}else{
				$m[2] = $type;
			}
			if($m[1]=='-'){
				if($m[2]=='season' && count($this->season) == 1){
					$this->season = range($this->season[0], (int)$m[3]);
				}elseif($m[2]=='episode' && count($this->episode) == 1){
					$this->episode = range($this->episode[0], (int)$m[3]);
				}
			}else{
				if($type=='season'){
					$this->season[] = (int)$m[3];
				}elseif($type=='episode'){
					$this->episode[] = (int)$m[3];
				}
			}
			$e = $m[4];
		}
	}
	function prefixIsFileHost($prefix){
		$prefix = strtoupper($prefix);
		if(class_exists('DDL')){
			$class = 'DDL';
		}else{
			$class = get_called_class();
		}
		if(in_array($prefix, $class::$FH_PREFIXES)){
			return true;
		}
		return false;
	}
	function parsePrefix(&$rls){
		$prefixes = array();
		while(preg_match('#^\[([^\]]+)\]#',$rls,$m)){
			$prefixes[] = $m[1];
			$rls = ltrim(substr($rls,strlen($m[0])));
		}
		if($prefixes){//Only do anime stuff if there are prefixes found
			if(!(count($prefixes)==1 && $this->prefixIsFileHost($prefixes[0]))){//First Check
				foreach($prefixes as $k=>$p){
					if($this->prefixIsFileHost($prefixes[0])){
						unset($prefixes[$k]);
					}
				}
				if($prefixes){
					foreach($prefixes as $k=>$p){
						//Check if is anime group
						
					}
				}
			}
		}
	}
	function Parse(){
		$rls = $this->rls;
		
		$this->parsePrefix($rls);
		
		$g = array();
		if(preg_match('#(S|E)([0-9]+)(.*)$#i',$rls,$g)){
			$this->parseReal($g,$rls);
		}elseif(preg_match('#(Season|Series|Episode)\s*([0-9]+)(.*)$#i',$rls,$g)){
			$this->parseNatural($g,$rls);
		}elseif(preg_match('#(.*)\s*([0-9]+)x([0-9]+)(.*)$#i',$rls,$g)){
			$this->parseReal2($g,$rls);
		}elseif(
				preg_match('#\-\s*([0-9]+)(.*)$#i',$rls,$g) || //First attempt 
				preg_match('#\s*([0-9]+)(.*)$#i',$rls,$g) //Second attempt
			){
			if($g[1] < 2000){
				$this->parseEpNum($g,$rls);
			}else{
				$this->isValid(false);
			}
		}else{
			$this->isValid(false);
		}
		
		if($this->episode){
			$this->ref_type = 'episode';
		}else{
			$this->ref_type = 'season';
		}
	}
	/**
	 * @return the $show
	 */
	public function getShow() {
		return $this->show;
	}

	/**
	 * @return the $season
	 */
	public function getSeason() {
		return $this->season;
	}

	/**
	 * @return the $episode
	 */
	public function getEpisode() {
		return $this->episode;
	}

	
	public function isRef($v){
		return ($this->ref_type==$v);
	}
}